<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Resource\Operation\UpdateOperation;
use Light\ObjectService\Exceptions\InvalidRequestException;

class JsonCreateOperationReader extends JsonUpdateOperationReader
{
	protected function prevalidate()
	{
		if (is_null($this->data))
		{
			throw new InvalidRequestException("A \"data\" argument is mandatory for a POST operation");
		}
	}
	
	public function read()
	{
		// TODO
	}
}