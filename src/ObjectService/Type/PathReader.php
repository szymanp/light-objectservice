<?php
namespace Light\ObjectService\Type;

use Light\Exception\Exception;
use Light\ObjectService\Exceptions\ResolutionException;
use Light\ObjectService\Expression\FindContextObject;
use Light\ObjectService\Expression\ParsedPathExpression;
use Light\ObjectService\Expression\ParsedRootPathExpression;
use Light\ObjectService\Expression\SelectExpressionSource;
use Light\ObjectService\Expression\WhereExpressionSource;
use Light\ObjectService\ObjectRegistry;

class PathReader
{
	/** @var \Light\ObjectService\Expression\ParsedPathExpression */
	private $path;
	
	/** @var \Light\ObjectService\ObjectRegistry */
	private $registry;
	
	/** @var PathReader_Value[] */
	private $resolved = array();
	
	/** @var \Light\ObjectService\Type\TypeHelper */
	private $typeHelper;
	
	/**
	 * @var \Light\ObjectService\Expression\SelectExpressionSource
	 */
	private $targetSelectionSource;
	
	public function __construct(ParsedPathExpression $path, ObjectRegistry $registry)
	{
		$this->path = $path;
		$this->registry = $registry;
		$this->typeHelper = new TypeHelper($this->registry);
		
		$type = $this->path->getRootType();
		if ($type instanceof CollectionType)
		{
			// An object provider
			$this->resolved[] = new PathReader_Collection($type);
		}
		else if ($type instanceof ComplexType)
		{
			$this->resolved[] = new PathReader_Object($type, $this->path->getRootObject());
		}
		else
		{
			throw new \LogicException();
		}
	}
	
	/**
	 * Sets the selection expression to be used when reading the last element in the path.
	 * 
	 * This expression is passed as hint to ObjectProvider::find to make it possible
	 * to pre-fetch any data that will be selected. If the last element in the path
	 * is not read using ObjectProvider::find, then using this method has no effect.  
	 *  
	 * @param SelectExpressionSource $selection
	 */
	public function setTargetSelection(SelectExpressionSource $selection)
	{
		$this->targetSelectionSource = $selection;
	}
	
	/**
	 * Reads a value.
	 * @throws ResolutionException
	 * @return \Light\ObjectService\Type\ResolvedValue
	 */
	public function read()
	{
		$partialpath = ($this->path instanceof ParsedRootPathExpression) ?
					   array($this->path->getRootResourceName())
					   : array();
		
		$pathElements = $this->path->getPathElements();
		$count 		  = count($pathElements);
		
		foreach($pathElements as $index => $element)
		{
			array_push($partialpath, $element);

			try
			{
				$this->readElement($element, $index + 1 == $count);
			}
			catch (TypeHelper_Exception $e)
			{
				throw new ResolutionException("%1 (while resolving path \"%3\" at element \"%2\")",
					$e->getMessage(),
					$element,
					$this->path->getPath(),
					$e);				
			}
			catch (PathReader_Exception $e)
			{
				throw new ResolutionException("%1 (while resolving path \"%3\" at element \"%2\")",
											  $e->getMessage(),
											  $element,
											  $this->path->getPath(),
											  $e);
			}
		}
		
		// Return the last value in the chain
		$last = $this->getCurrent();
		if (!$last->hasValue())
		{
			throw new ResolutionException("Resolution of path \"%1\" did not produce any value", $this->path->getPath());
		}
		
		return new ResolvedValue($last->getType(), $this->path, $last->value);
	}
	
	private function readElement($element, $isLastElement)
	{
		$current = $this->getCurrent();
		
		if (is_string($element))
		{
			// The element is either a property name of an object or a key in an array.
			if ($current instanceof PathReader_Collection)
			{
				// If the collection consists of a single element, then we use that element
				// as the subject for reading the property.
				$currentObject = $current->getFirstElementAsObject();
			}
			else
			{
				$currentObject = $current->asObject();
			}
			
			// If the return type of this property is a collection and the base type
			// of the collection is an object provider, then instead of reading this
			// property directly we will invoke a find() on the provider.
			$fieldSpec = $currentObject->type->getSpecification()->getField($element);
			if ($fieldSpec && !is_null($type = $this->typeHelper->getObjectProviderOrNull($fieldSpec)))
			{
				$this->resolved[] = new PathReader_Collection($type);
			}
			else
			{
				$newObject = $currentObject->type->readProperty($currentObject->value, $element);
				$type = $fieldSpec?$this->typeHelper->getTypeForField($fieldSpec) : null;
				$this->pushNewValue($newObject, $type);
			}
		}
		elseif ($element instanceof WhereExpressionSource)
		{
			// The underlying object must be of a type that is registered as a model.
			// We invoke a find() on that model with the context set to the parent of that object.
			// E.g. /models/tag/123/comments/_1
			// - the underlying object is a /models/comment
			// - the parent object is a /models/tag
			$currentColl = $current->asCollection();
		
			if ($currentColl->type instanceof ObjectProvider)
			{
				$whereExpr = $element->compile($currentColl->type);
				
				$context = new FindContextObject();
				$context->setContextObject($currentColl->value);
				
				if ($isLastElement && $this->targetSelectionSource)
				{
					$selectionHint = $this->targetSelectionSource->compile($currentColl->type->getBaseType());
					$context->setSelectionHint($selectionHint);
				}
				
				$newObject = $currentColl->type->find($whereExpr, $context);
					
				// The result must be an array
				if (!is_array($newObject)
					&& !($newObject instanceof \ArrayAccess)
					&& !($newObject instanceof \Iterator))
				{
					throw new PathReader_Exception("%1::find() returned a non-array-like result (%2)", get_class($element), $newObject);
				}
					
				$this->pushNewValue($newObject, $currentColl->type);
			}
			else
			{
				throw new PathReader_Exception("Cannot execute a query on a non-provider object (PHP class is \"%1\")", get_class($element->value));
			}
		}
		elseif (is_integer($element))
		{
			// The element is either an ID of a model object or a index in an array.
			
			$currentColl = $current->asCollection();
			
			if (is_array($currentColl->value))
			{
				$newObject = @ $currentColl->value[$element];
			}
			else if ($currentColl->value instanceof \ArrayAccess)
			{
				$newObject = $currentColl->value->offsetGet($element);
			}
			else if ($currentColl->type instanceof ObjectProvider)
			{
				$baseType = $currentColl->type->getBaseType();
				if ($baseType instanceof ComplexType)
				{
					$whereExpr = $baseType->getPrimaryKeyWhereExpression($element);
				}
				else
				{
					throw new PathReader_Exception("Access by primary-key is only supported for complex types");
				}
				
				$context = new FindContextObject();
				$context->setContextObject(null);
				
				if ($isLastElement && $this->targetSelectionSource)
				{
					$selectionHint = $this->targetSelectionSource->compile($baseType);
					$context->setSelectionHint($selectionHint);
				}
					
				$newObject = $currentColl->type->find($whereExpr, $context);

				if (count($newObject) == 0)
				{
					$newObject = null;
				}
				else
				{
					$newObject = $currentColl->type->getSingleElement($newObject);
				}
			}
			
			$this->pushNewValue($newObject, $currentColl->type->getBaseType());
		}
		else
		{
			throw new \LogicException(get_class($element));
		}
	}
	
	private function pushNewValue($value, Type $type = null)
	{
		if (is_null($type))
		{
			// We need to guess the type based on the actual type of $value.
			if (is_object($value))
			{
				$type = $this->typeHelper->getType(get_class($value), false);
				
				if ($type instanceof SimpleType)
				{
					$this->resolved[] = new PathReader_Object($type, $value);
				}
				else if ($type instanceof ComplexType)
				{
					$this->resolved[] = new PathReader_Object($type, $value);
				}
				else
				{
					throw new \LogicException();
				}
			}
			else if (is_scalar($value)
					 || is_array($value))	// Even though an array is not a scalar, we treat it as such
					 						// as we want to express that it is a value that cannot be resolved further
			{
				$this->resolved[] = new PathReader_Scalar(BuiltinType::getInstance(), $value);
			}
			else 
			{
				// todo A null value.
				$this->resolved[] = new PathReader_Scalar(BuiltinType::getInstance(), null);
			}
		}
		elseif ($type instanceof SimpleType)
		{
			// todo Convert into proper class

			$this->resolved[] = new PathReader_Scalar($type, $value);
		}
		elseif ($type instanceof ComplexType)
		{
			$class = $type->getClassName();
			
			// todo Convert into proper class
			
			$this->resolved[] = new PathReader_Object($type, $value);
		}
		elseif ($type instanceof CollectionType)
		{
			$this->resolved[] = new PathReader_Collection($type, $value);
		}
		else 
		{
			throw new \LogicException();
		}
	}
	
	/**
	 * @return PathReader_Value
	 */
	private function getCurrent()
	{
		return end($this->resolved);
	}
}

abstract class PathReader_Value
{
	/** @var mixed */
	public $value;
	
	public function hasValue()
	{
		return !is_null($this->value);
	}
	
	/**
	 * Returns the value.
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Returns the type of the value.
	 * @return \Light\ObjectService\Type\Type
	 */
	abstract public function getType();

	/**
	 * Returns the value as a Collection.
	 * @throws PathReader_Exception
	 * @return \Light\ObjectService\Type\PathReader_Collection
	 */
	public function asCollection()
	{
		if ($this instanceof PathReader_Collection)
		{
			return $this;
		}
		throw new PathReader_Exception("Value is not a collection");
	}
	
	/**
	 * Returns the value as an Object.
	 * @throws PathReader_Exception
	 * @return \Light\ObjectService\Type\PathReader_Object
	 */
	public function asObject()
	{
		if ($this instanceof PathReader_Object)
		{
			return $this;
		}
		throw new PathReader_Exception("Value is not an object");
	}
	
	/**
	 * Returns the value as a Scalar.
	 * @throws PathReader_Exception
	 * @return \Light\ObjectService\Type\PathReader_Scalar
	 */
	public function asScalar()
	{
		if ($this instanceof PathReader_Scalar)
		{
			return $this;
		}
		throw new PathReader_Exception("Value is not a scalar");
	}
}

class PathReader_Object extends PathReader_Value
{
	/** @var ComplexType */
	public $type;

	public function __construct(ComplexType $type, $object = null)
	{
		$this->value = $object;
		$this->type	  = $type;
	}
	
	/**
	 * Returns the type of the value.
	 * @return \Light\ObjectService\Type\ComplexType
	 */
	public function getType()
	{
		return $this->type;
	}
}

class PathReader_Scalar extends PathReader_Value
{
	/** @var SimpleType */
	public $type;
	
	public function __construct(SimpleType $type, $value = null)
	{
		$this->type = $type;
		$this->value = $value;
	}	

	/**
	 * Returns the type of the value.
	 * @return \Light\ObjectService\Type\SimpleType
	 */
	public function getType()
	{
		return $this->type;
	}
}

class PathReader_Collection extends PathReader_Value
{
	/** @var CollectionType */
	public $type;

	public function __construct(CollectionType $type, $value = null)
	{
		$this->type = $type;
		$this->value = $value;
	}
	
	/**
	 * Returns the type of the collection.
	 * @return \Light\ObjectService\Type\CollectionType
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * @return boolean	true if the value is an array
	 */
	public static function isCollection($value)
	{
		return is_array($value)
			|| $value instanceof \ArrayAccess
			|| $value instanceof \Iterator;
	}
	
	/**
	 * Returns the first and only element of this collection as an Object.
	 * @throws PathReader_Exception
	 * @return \Light\ObjectService\Type\PathReader_Object
	 */
	public function getFirstElementAsObject()
	{
		$baseType = $this->type->getBaseType();
		if (!($baseType instanceof ComplexType))
		{
			throw new PathReader_Exception("Value is not an object");
		}
		
		return new PathReader_Object($baseType, $this->getFirstElement());
	}
	
	public function getFirstElement()
	{
		try
		{
			return $this->type->getSingleElement($this->value);
		}
		catch (\Exception $e)
		{
			throw new PathReader_Exception($e);
		}
	}
}

final class PathReader_Exception extends Exception
{
}