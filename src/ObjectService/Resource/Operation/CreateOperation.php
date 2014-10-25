<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectService\Exceptions\TypeException;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Type\ComplexTypeInterfaces;
use Light\ObjectService\Type\Util\CreationDeletionContextObject;

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

	public function execute(ExecutionParameters $params)
	{
		if ($this->type instanceof ComplexTypeInterfaces\Create)
		{
			$context = new CreationDeletionContextObject();
			$newObject = $this->type->createObject($context);

			// TODO
			return null;
		}
		else
		{
			throw new TypeException("Type %1 does not support object creation", $this->type->getUri());
		}
	}
}