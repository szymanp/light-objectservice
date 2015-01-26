<?php
namespace Light\ObjectService\Json\Request\Operation;

use Light\ObjectService\Json\Request\Reader;
use Light\ObjectService\Json\Request\ResourceSpecificationReader;
use Light\ObjectService\Resource\Operation\AppendOperation;

class AppendOperationReader extends Reader
{
	/**
	 * Reads a delete operation from a JSON object.
	 * @param \stdclass $json
	 * @return AppendOperation
	 * @throws \Light\ObjectService\Exception\MalformedRequest
	 */
	public function read(\stdclass $json)
	{
		$appendOperation = new AppendOperation();

		$resourceSpecificationReader = new ResourceSpecificationReader($this->getExecutionParameters());
		$resourceSpecification = $resourceSpecificationReader->read($json);
		$resource = $resourceSpecification->resolve($this->getExecutionParameters());

		$appendOperation->setElementResource($resource);

		return $appendOperation;
	}
}