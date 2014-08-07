<?php

namespace Light\ObjectService\Builder;

use Light\Exception\Exception;

class FieldBuilder extends BaseFieldBuilder
{
	/** @var ComplexSpecBuilder */
	private $specBuilder;
	
	private $readable = true;
	private $writable = true;
	
	/** @var callback */
	private $getter;
	
	/** @var callback */
	private $setter;
	
	public function __construct(ComplexSpecBuilder $parent, $name)
	{
		$this->specBuilder 	= $parent;
		$this->propertyName = $name;
	}
	
	/**
	 * Sets the name of the underlying property on the model object.
	 * @param string $name
	 * @return \Light\ObjectService\Builder\FieldBuilder
	 */
	public function property($name)
	{
		$this->propertyName = $name;
		
		// Make the original property inaccessible
		$this->specBuilder->exclude($name);
		
		return $this;
	}
	
	/**
	 * Sets the type of the property.
	 * @param string $typeName
	 * @return \Light\ObjectService\Builder\FieldBuilder
	 */
	public function type($typeName)
	{
		parent::type($typeName);
		return $this;
	}
	
	/**
	 * Sets the type of the property to a collection of the specified base type.
	 * @param string $typeName	Base type.
	 * @return \Light\ObjectService\Builder\FieldBuilder
	 */
	public function collectionOfType($typeName)
	{
		parent::collectionOfType($typeName);
		return $this;
	}
	
	/**
	 * Marks this field as the primary key.
	 * @return \Light\ObjectService\Builder\FieldBuilder
	 */
	public function primaryKey()
	{
		$this->specBuilder->keyField($this);
		return $this;
	}
	
	public function objectProvider($objectProvider)
	{
		// TODO
		return $this;
	}
	
	/**
	 * Makes this property read-only.
	 * @return \Light\ObjectService\Builder\FieldBuilder
	 */
	public function readonly()
	{
		$this->writable = false;
		return $this;
	}
	
	/**
	 * Sets a getter for reading the value of this field.
	 * @param array|Closure $callback
	 * @return \Light\ObjectService\Builder\FieldBuilder
	 */
	public function getter($callback)
	{
		$this->getter = $callback;
		return $this;
	}

	/**
	 * Sets a setter for setting a new value of this field.
	 * @param array|Closure $callback
	 * @return \Light\ObjectService\Builder\FieldBuilder
	 */
	public function setter($callback)
	{
		$this->setter = $callback;
		return $this;
	}

	/**
	 * @return ComplexSpecBuilder
	 */
	public function done()
	{
		return $this->specBuilder;
	}
	
	// getters
	
	public function getGetter()
	{
		return $this->getter;
	}

	public function getSetter()
	{
		return $this->setter;
	}
	
	public function isReadable()
	{
		return $this->readable;
	}

	public function isWritable()
	{
		return $this->writable;
	}
}
