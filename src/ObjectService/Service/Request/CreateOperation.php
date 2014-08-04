<?php
namespace Light\ObjectService\Service\Request;

use Light\Exception\InvalidParameterType;
use Light\ObjectService\Model\ComplexType;

class CreateOperation extends UpdateOperation
{
	/**
	 * @var \Light\ObjectService\Model\ComplexType
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
	 * @return \Light\ObjectService\Model\ComplexType
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * Returns the resource created in this operation.
	 * @return \Light\ObjectService\Model\ResolvedValue
	 */
	public function getNewResource()
	{
		// TODO
	}
}