<?php
namespace Light\ObjectService\Model;

use Light\ObjectService\Builder\ComplexSpecBuilder;
use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Expression\SelectExpression;
use Light\Exception\Exception;

/**
 * A description of an object type.
 * 
 */
class ComplexType extends Type
{
	/** @var \Light\ObjectService\Builder\ComplexSpecBuilder */
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
	 * @return \Light\ObjectService\Builder\ComplexSpecBuilder
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
	 * This method can be overriden in subclasses to provide a custom "getterr" implementation
	 * for objects of belonging to this complex type.

	 * @param object	$object
	 * @param string	$propertyName
	 * @return mixed
	 */
	public function readProperty($object, $propertyName)
	{
		// TODO
	}

	/**
	 * Sets the value for the named property on the specified object.
	 * 
	 * This method can be overriden in subclasses to provide a custom "setter" implementation
	 * for objects of belonging to this complex type.
	 * 
	 * @param object	$object
	 * @param string	$propertyName
	 * @param mixed		$value
	 */
	public function writeProperty($object, $propertyName, $value)
	{
		// TODO
	}
}