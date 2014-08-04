<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Service\Request\UpdateOperation;
use Light\ObjectService\Exceptions\InvalidRequestException;

class JsonUpdateOperationReader extends JsonOperationReader
{
	protected function prevalidate()
	{
		if (is_null($this->data))
		{
			throw new InvalidRequestException("A \"data\" argument is mandatory for a PUT operation");
		}
	}
	
	public function read()
	{
		$oper = new UpdateOperation();
		$this->setupOperation($oper);
		
		foreach($this->data as $fieldName => $value)
		{
			if (is_object($value))
			{
				$opReader = JsonOperationReader::createChild($oper, $value);
				$oper->setFieldOperation($fieldName, $opReader->read());
			}
			else
			{
				$oper->setFieldValue($fieldName, $value);
			}
		}
		
		return $oper;
	}
}