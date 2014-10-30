<?php
namespace Light\ObjectService\Service\Util;

use Light\ObjectService\EndpointRegistry;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Transaction\Transaction;

class ExecutionParametersObject implements ExecutionParameters
{
	private $transaction;
	private $registry;
	
	public function setTransaction(Transaction $tx)
	{
		$this->transaction = $tx;
	}
	
	public function setEndpointRegistry(EndpointRegistry $registry)
	{
		$this->registry = $registry;
	}
	
	public function getTransaction()
	{
		return $this->transaction;
	}
	
	public function getEndpointRegistry()
	{
		return $this->registry;
	}

	/**
	 * Copy values from another ExecutionParameters object.
	 * @param ExecutionParameters $params
	 */
	public function copyFrom(ExecutionParameters $params)
	{
		$this->transaction = $params->getTransaction();
		$this->registry = $params->getEndpointRegistry();
	}
}