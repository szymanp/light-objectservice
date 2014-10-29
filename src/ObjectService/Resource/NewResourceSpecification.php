<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Exceptions\TypeException;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Resource\Operation\ResourceUpdateSpecification;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Type\ComplexTypeInterfaces\Create;
use Light\ObjectService\Type\Util\CreationDeletionContextObject;

class NewResourceSpecification extends ResourceSpecification
{
	/** @var ComplexType */
	private $complexType;

	/** @var ResourceUpdateSpecification */
	private $resourceUpdateSpecification;

	public function __construct(ComplexType $complexType, ResourceUpdateSpecification $updateSpec = null)
	{
		$this->complexType = $complexType;
		$this->resourceUpdateSpecification = $updateSpec;
	}

	/**
	 * @param ExecutionParameters $parameters
	 * @throws TypeException
	 * @return ResolvedValue
	 */
	public function resolve(ExecutionParameters $parameters)
	{
		if ($this->complexType instanceof Create)
		{
			$context = new CreationDeletionContextObject();
			$newObject = $this->complexType->createObject($context);

			$newResource = new ResolvedValue($this->complexType, $newObject);

			if ($this->resourceUpdateSpecification)
			{
				$this->resourceUpdateSpecification->update($newResource, $parameters);
			}

			return $newResource;
		}
		else
		{
			throw new TypeException("Type %1 does not support object creation", $this->complexType->getUri());
		}
	}
}