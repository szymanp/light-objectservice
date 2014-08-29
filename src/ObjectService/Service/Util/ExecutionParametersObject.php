<?php
namespace Light\ObjectService\Service\Util;

use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Transaction\Transaction;
use Light\ObjectService\ObjectRegistry;

class ExecutionParametersObject implements ExecutionParameters
{
	private $transaction;
	private $registry;
	
	public function setTransaction(Transaction $tx)
	{
		$this->transaction = $tx;
	}
	
	public function setObjectRegistry(ObjectRegistry $registry)
	{
		$this->registry = $registry;
	}
	
	public function getTransaction()
	{
		return $this->transaction;
	}
	
	public function getObjectRegistry()
	{
		return $this->registry;
	}
}