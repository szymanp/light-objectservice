<?php

namespace Light\ObjectService\Builder;

use Light\Exception\Exception;

class RestrictionBuilder extends BaseFieldBuilder
{
	/** @var CollectionSpecBuilder */
	private $specBuilder;
	
	private $isCriterion = false;
	
	public function __construct(CollectionSpecBuilder $parent, $name)
	{
		$this->specBuilder 	= $parent;
		$this->propertyName = $name;
	}
	
	/**
	 * Sets the type of the property.
	 * @param string $typeName
	 * @return \Light\ObjectService\Builder\RestrictionBuilder
	 */
	public function type($typeName)
	{
		parent::type($typeName);
		return $this;
	}
	
	/**
	 * Sets the type of the property to a collection of the specified base type.
	 * @param string $typeName	Base type.
	 * @return \Light\ObjectService\Builder\RestrictionBuilder
	 */
	public function collectionOfType($typeName)
	{
		parent::collectionOfType($typeName);
		return $this;
	}
	
	/**
	 * Marks this field as a criterion restriction.
	 * @return \Light\ObjectService\Builder\RestrictionBuilder
	 */
	public function criterion()
	{
		$this->isCriterion = true;
		return $this;
	}
	
	/**
	 * @return CollectionSpecBuilder
	 */
	public function done()
	{
		return $this->specBuilder;
	}
	
	// getters
	
	public function isCriterion()
	{
		return $this->isCriterion;
	}
}
