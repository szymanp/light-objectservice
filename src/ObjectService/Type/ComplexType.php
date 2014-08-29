<?php
namespace Light\ObjectService\Type;

use Light\ObjectService\Type\Builder\ComplexSpecBuilder;
use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Expression\SelectExpression;
use Light\Exception\Exception;
use Light\ObjectService\Exceptions\ResolutionException;
use Light\ObjectService\Transaction\Transaction;
use Light\Data\Helper;

/**
 * A description of an object type.
 * 
 */
class ComplexType extends Type
{
	/** @var \Light\ObjectService\Type\Builder\ComplexSpecBuilder */
	private $spec;
	
	/** @var string */
	private $name;
	
	/** @var \Light\ObjectService\Expression\SelectExpression */
	private $defaultSelection;
	
	public function __construct()
	{
		$this->spec = new ComplexSpecBuilder();
	}
	
	/**
	 * Returns a specification for this type.
	 * @return \Light\ObjectService\Type\Builder\ComplexSpecBuilder
	 */
	final public function getSpecification()
	{
		return $this->spec;
	}
	
	/**
	 * Returns a name of this type.
	 * @return string
	 */
	final public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Returns a WHERE expression filtering on the primary key.
	 * @param integer	$pk
	 * @throws Exception
	 * @return \Light\ObjectService\Expression\WhereExpression
	 */
	final public function getPrimaryKeyWhereExpression($pk)
	{
		$keyField = $this->getSpecification()->getKeyField();
		if (!$keyField)
		{
			throw new Exception("No key field defined in complex type %1", get_class($this));
		}
		
		return WhereExpression::create($this)
			   ->setValue($this->getSpecification()->getFieldName($keyField),
			   			  new Criterion($pk));
	}
	
	/**
	 * Returns a select expression that is the default for this type.
	 * @return \Light\ObjectService\Expression\SelectExpression
	 */
	final public function getDefaultSelection()
	{
		// todo
		// For now, the default selection includes all fields.
		if (is_null($this->defaultSelection))
		{
			$this->defaultSelection = SelectExpression::create($this)
									  ->fields($this->spec->getAllFields());
		}
		return $this->defaultSelection;
	}
	
	/**
	 * Reads a value from the named property on the specified object.
	 * 
	 * This method will read the property using a getter defined on the field builder, if any;
	 * otherwise it will attempt to read the property directly from the object.
	 * 
	 * @param object	$object
	 * @param string	$propertyName
	 * @see readPropertyInternal()
	 * @throws ResolutionException	If the property doesn't exist or cannot be read.
	 * @return mixed	the property value
	 */
	final public function readProperty($object, $propertyName)
	{
		$fieldSpec = $this->spec->getFieldOrThrow($propertyName);
		
		if (!$this->spec->canRead($propertyName))
		{
			throw new ResolutionException("Field %1::%2 is not readable", $this->getName(), $propertyName);
		}
		
		if (!is_null($getter = $fieldSpec->getGetter()))
		{
			return call_user_func($getter, $object);
		}
		else
		{
			return $this->readPropertyInternal($object, $propertyName);
		}
	}

	/**
	 * Sets the value for the named property on the specified object.
	 * 
	 * This method will set the property using a setter defined on the field builder, if any;
	 * otherwise it will attempt to set the property directly on the object.
	 * 
	 * @param object		$object
	 * @param string		$propertyName
	 * @param mixed			$value
	 * @param Transaction	$transaction
	 * @see writePropertyInternal()
	 */
	final public function writeProperty($object, $propertyName, $value, Transaction $transaction)
	{
		$fieldSpec = $this->spec->getFieldOrThrow($propertyName);
		
		if (!$this->spec->canWrite($propertyName))
		{
			throw new ResolutionException("Field %1::%2 is not writable", $this->getName(), $propertyName);
		}
		
		if (!$transaction->isOpen())
		{
			$transaction->begin();
		}
		
		if (!is_null($setter = $fieldSpec->getSetter()))
		{
			call_user_func($setter, $object, $value, $transaction);
		}
		else
		{
			$this->writePropertyInternal($object, $propertyName, $value, $transaction);
		}
	}

	/**
	 * Returns the PHP class name of the objects supported by this complex type.
	 * @return string
	 */
	public function getClassName()
	{
		return $this->spec->getClassname();
	}
	
	/**
	 * Reads a value from the named property on the specified object.
	 * 
	 * This method can be overriden in subclasses to provide a custom "getter" implementation
	 * for objects of belonging to this complex type.
	 * 
	 * @param object	$object
	 * @param string	$propertyName
	 * @throws ResolutionException	If the property doesn't exist or cannot be read.
	 * @return mixed the property value
	 */
	protected function readPropertyInternal($object, $propertyName)
	{
		try
		{
			$wrapped = Helper::wrap($object);
			return $wrapped->getValue($propertyName);
		}
		catch (\Exception $e)
		{
			throw new ResolutionException("Field %1::%2 cannot be read: %3", $this->getName(), $propertyName, $e->getMessage(), $e);
		}
	}
	
	/**
	 * Sets a value of the named property on the specified object.
	 *
	 * This method can be overriden in subclasses to provide a custom "setter" implementation
	 * for objects belonging to this complex type.
	 * 
	 * @param object		$object
	 * @param string		$propertyName
	 * @param mixed			$value
	 * @param Transaction	$tx
	 */
	protected function writePropertyInternal($object, $propertyName, $value, Transaction $tx)
	{
		try
		{
			$wrapped = Helper::wrap($object);
			$wrapped->setValue($propertyName, $value);
		}
		catch (\Exception $e)
		{
			throw new ResolutionException("Field %1::%2 cannot be written: %3", $this->getName(), $propertyName, $e->getMessage(), $e);
		}
	}
}