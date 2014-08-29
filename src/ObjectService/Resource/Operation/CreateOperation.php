<?php
namespace Light\ObjectService\Resource\Operation;

use Light\Exception\InvalidParameterType;
use Light\ObjectService\Type\ComplexType;

class CreateOperation extends UpdateOperation
{
	/**
	 * @var \Light\ObjectService\Type\ComplexType
	 */
	private $type;

	/**
	 * Sets the type of the object to be created.
	 * @param ComplexType $type
	 */
	public function setType(ComplexType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * Returns the type of the object to be created.
	 * @return \Light\ObjectService\Type\ComplexType
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * Returns the resource created in this operation.
	 * @return \Light\ObjectService\Type\ResolvedValue
	 */
	public function getNewResource()
	{
		// TODO
	}
}