<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectAccess\Exception\ResourceException;
use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Resource\ResolvedCollectionValue;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectService\Exception\MalformedRequest;

/**
 * Deletes an object or removes a resource from a collection.
 */
class DeleteOperation extends Operation
{
	/** @var Scope */
	private $scope;

	/**
	 * Sets the scope for identifying collection elements to be removed.
	 * @param Scope $scope
	 */
	public function setScope(Scope $scope)
	{
		$this->scope = $scope;
	}

	/**
	 * Executes the operation.
	 * @param ResolvedResource    $resource
	 * @param ExecutionParameters $parameters
	 */
	public function execute(ResolvedResource $resource, ExecutionParameters $parameters)
	{
		if ($resource instanceof ResolvedCollectionResource)
		{
			// TODO
		}
		elseif ($resource instanceof ResolvedCollectionValue)
		{
			// TODO
		}
		elseif ($resource instanceof ResolvedObject)
		{
			if (!is_null($this->scope))
			{
				throw new MalformedRequest("A scope cannot be set when deleting an object");
			}

			$resource->getTypeHelper()->deleteResource($parameters->getTransaction());
		}
		else
		{
			throw new ResourceException("Only objects and collection elements can be deleted");
		}
	}

}