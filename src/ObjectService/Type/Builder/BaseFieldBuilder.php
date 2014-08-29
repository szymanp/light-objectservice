<?php

namespace Light\ObjectService\Type\Builder;

/**
 * Base class for field builders.
 *
 */
abstract class BaseFieldBuilder
{
	/**
	 * The name of the property on the model object.
	 * @var string
	 */
	protected $propertyName;
	
	/**
	 * The target type of the values in this property. 
	 * @var string
	 */
	protected $type;
	
	protected $isCollection = false;
	
	/**
	 * Sets the type of the property.
	 * @param string $typeName
	 * @return \Light\ObjectService\Type\Builder\BaseFieldBuilder
	 */
	public function type($typeName)
	{
		if (substr($typeName, -2, 2) == "[]")
		{
			$this->type = substr($typeName, 0, -2);
			$this->isCollection = true;
		}
		else
		{
			$this->type = $typeName;
			$this->isCollection = false;
		}
		return $this;
	}
	
	/**
	 * Sets the type of the property to a collection of the specified base type.
	 * @param string $typeName	Base type.
	 * @return \Light\ObjectService\Type\Builder\BaseFieldBuilder
	 */
	public function collectionOfType($typeName)
	{
		$this->type = $typeName;
		$this->isCollection = true;
		return $this;
	}
	
	// getters
	
	public function getPropertyName()
	{
		return $this->propertyName;
	}
	
	public function isCollection()
	{
		return $this->isCollection;
	}
	
	public function getTypeName()
	{
		return $this->type;
	}
}
