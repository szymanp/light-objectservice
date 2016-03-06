<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectAccess\Exception\ResourceException;
use Light\ObjectAccess\Resource\ResolvedCollection;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedValue;
use Szyman\ObjectService\Service\ExecutionParameters;

/**
 * Appends a resource to a collection.
 */
class AppendOperation extends Operation
{
	/** @var ResolvedResource */
	private $elementResource;

	/**
	 * Sets the resource to be appended to the collection.
	 * @param ResolvedResource $elementResource
	 */
	public function setElementResource($elementResource)
	{
		$this->elementResource = $elementResource;
	}

	/**
	 * @return ResolvedResource
	 */
	public function getElementResource()
	{
		return $this->elementResource;
	}

	/**
	 * Executes the operation.
	 * @param ResolvedResource    $resource
	 * @param ExecutionParameters $parameters
	 * @throws ResourceException
	 * @throws \Light\ObjectAccess\Exception\TypeException
	 */
	public function execute(ResolvedResource $resource, ExecutionParameters $parameters)
	{
		if ($resource instanceof ResolvedCollection)
		{
			if ($this->elementResource instanceof ResolvedValue)
			{
				$value = $this->elementResource->getValue();
			}
			else
			{
				throw new ResourceException("The resource to be appended to the collection does not have a value");
			}
			$resource->getTypeHelper()->appendValue($resource, $value, $parameters->getTransaction());
		}
		else
		{
			throw new ResourceException("The subject resource is not a collection");
		}
	}
}