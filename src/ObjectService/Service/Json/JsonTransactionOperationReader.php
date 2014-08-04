<?php
namespace Light\ObjectService\Service\Json;

class JsonTransactionOperationReader extends JsonOperationReader
{
	protected function prevalidate()
	{
		
	}
	
	public function read()
	{
		//$oper = new TransactionOperation();
		
		foreach($this->data as $operationData)
		{
			if (is_object($value))
			{
				// TODO
				$fieldOper = JsonOperationReader::createChild($this, $jsondata);
				
				
				$oper->setFieldOperation($fieldName, $fieldOper->read());
			}
			else
			{
				$oper->setFieldValue($fieldName, $value);
			}
		}
		
		return $oper;
	}
}