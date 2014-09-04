<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Resource\Operation\Operation;

/**
 * Returns a target resource by optionally applying an operation to an existing or new resource.
 *
 */
abstract class ResourceSpecification implements FieldTransformation
{
	/**
	 * Operation to be executed on the resource. 
	 * @var \Light\ObjectService\Resource\Operation\Operation
	 */
	private $operation;
	
	/**
	 * Returns the operation to be applied to the base resource.
	 * @return \Light\ObjectService\Resource\Operation\Operation
	 */
	final public function getOperation()
	{
		return $this->operation;
	}
	
	final public function setOperation(Operation $operation)
	{
		$this->operation = $operation;
		return $this;
	}

	/**
	 * @param ExecutionParameters $parameters
	 * @return ResourceSpecificationResult
	 */
	final public function execute(ExecutionParameters $parameters)
	{
		$result = $this->createResultObject();
		$result->setBaseResource($this->readBaseResource($parameters));

		if ($this->operation)
		{
			// TODO invoke operation, get target resource
		}
		else
		{
			$result->setTargetResource($result->getBaseResource());
		}
		
		return $result;
	}

	/**
	 * @return ResourceSpecificationResult
	 */
	protected function createResultObject()
	{
		return new ResourceSpecificationResult();
	}

	protected abstract function readBaseResource(ExecutionParameters $parameters);
}
