<?php 

namespace Light\ObjectService\Expression;

use Light\ObjectService\Model\CollectionType;
use Light\ObjectService\Model\Type;
use Light\ObjectService\Model\ComplexType;
use Light\ObjectService\Model\SimpleType;
use Light\Exception\InvalidParameterType;
use Light\ObjectService\Exceptions\TypeException;
use Light\ObjectService\Model\TypeHelper;

final class WhereExpression implements WhereExpressionSource
{
	/** @var array<string, \Light\ObjectService\Expression\Value|\Light\ObjectService\Expression\Criterion[]> */
	private $values = array();
	
	/** @var \Light\ObjectService\Model\CollectionType */
	private $type;
	
	/**
	 * Returns a new WhereExpression object.
	 * @param CollectionType|ComplexType $type
	 * @return \Light\ObjectService\Expression\WhereExpression
	 */
	public static function create(Type $type)
	{
		if ($type instanceof ComplexType)
		{
			$type = $type->getObjectRegistry()->getCollectionType($type->getClassName());
		}
		else if ($type instanceof SimpleType)
		{
			throw new InvalidParameterType('$typee', $type);
		}
		
		return new self($type);
	}
	
	/**
	 * Constructs a new WhereExpression object.
	 * @param CollectionType $type
	 */
	public function __construct(CollectionType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * Adds a restriction on a value.
	 * @param string	$name
	 * @param mixed		$value
	 * @return \Light\ObjectService\Expression\WhereExpression
	 */
	public function setValue($name, $value)
	{
		$fieldSpec = $this->type->getSpecification()->getFieldOrThrow($name);
		$addAsList 	= false;
		$valid 		= true;
		
		if ($value instanceof Criterion)
		{
			$addAsList 	= true;
			$valid 		= $fieldSpec->isCriterion();
		}
		elseif ($value instanceof Value)
		{
			$addAsList = $fieldSpec->isCollection();
			$valid	   = !$fieldSpec->isCriterion();
		}
		elseif ($value instanceof WhereExpression)
		{
			$type 		= TypeHelper::create($this->type->getObjectRegistry())->getTypeForField($fieldSpec);
			$valid 		= !$fieldSpec->isCriterion()
						  && ($type instanceof ComplexType
						  	  || $type instanceof CollectionType
						  		 && $type->getBaseType() instanceof CompleType);
			
			$addAsList = ($type instanceof CollectionType);
		}
		else
		{
			throw new InvalidParameterType('$value', $value);
		}
		
		if (!$valid)
		{
			throw new TypeException("Field \"%1\" accepts values of type \"%2\"", $name, $fieldSpec->getTypeName());
		}
		
		if ($addAsList)
		{
			if (!isset($this->values[$name]))
			{
				$this->values[$name] = array($value);
			}
			else
			{
				$this->values[$name][] = $value;
			}
		}
		else
		{
			$this->values[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Runs a callback function for each defined restriction.
	 * @param string|array	$names		Restriction names.
	 * @param callback		$callback
	 * @return \Light\ObjectService\Expression\WhereExpression
	 */
	public function with($names, $callback)
	{
		if (is_string($names))
		{
			$names = array($names);
		}
		
		foreach($names as $name)
		{
			if (!isset($this->values[$name])) continue;
			
			$values = $this->values[$name];
			
			if (is_array($values))
			{
				foreach($values as $value)
				{
					call_user_func($callback, $value, $name);
				}
			}
			else
			{
				call_user_func($callback, $values, $name);
			}
		}
		
		return $this;
	}
	
	/**
	 * Returns a list of restriction values defined for the named property. 
	 * @param string $name
	 * @return mixed
	 */
	public function getValues($name)
	{
		return $this->values[$name];
	}
	
	/**
	 * Returns the type that this where expression applies to.
	 * @return \Light\ObjectService\Model\CollectionType
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Expression\WhereExpressionSource::compile()
	 */
	public function compile(CollectionType $type)
	{
		if ($type === $this->type)
		{
			return $this;
		}
		throw new TypeException("This where expression is already compiled for type \"%1\"", $this->type);
	}
}