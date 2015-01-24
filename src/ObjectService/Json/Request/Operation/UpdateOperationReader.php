<?php
namespace Light\ObjectService\Json\Request\Operation;

use Light\ObjectService\Json\Request\Reader;
use Light\ObjectService\Json\Request\ResourceSpecificationReader;
use Light\ObjectService\Resource\Operation\UpdateOperation;

class UpdateOperationReader extends Reader
{
	/**
	 * Reads an update operation from a JSON object.
	 * @param \stdClass $json
	 * @return UpdateOperation
	 */
	public function read(\stdClass $json)
	{
		$updateOperation = new UpdateOperation();

		foreach($json as $fieldName => $value)
		{
			if (is_object($value))
			{
				$resourceSpecificationReader = new ResourceSpecificationReader($this->getExecutionParameters());
				$updateOperation->setResource($fieldName, $resourceSpecificationReader->read($value));
			}
			else
			{
				$updateOperation->setValue($fieldName, $value);
			}
		}

		return $updateOperation;
	}
}