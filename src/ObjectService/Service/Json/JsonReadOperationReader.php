<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Resource\Operation\ReadOperation;
use Light\ObjectService\Exceptions\InvalidRequestException;

class JsonReadOperationReader extends JsonOperationReader
{
	protected function prevalidate()
	{
		if (!is_null($this->data))
		{
			throw new InvalidRequestException("No \"data\" argument can be specified for a GET operation");
		}
	}
	
	public function read()
	{
		$oper = new ReadOperation();
		$this->setupOperation($oper);
		
		return $oper;
	}
}