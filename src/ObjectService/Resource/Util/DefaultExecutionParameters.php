<?php
namespace Light\ObjectService\Resource\Util;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Service\EndpointRegistry;

class DefaultExecutionParameters implements ExecutionParameters
{
	/** @var EndpointRegistry */
	private $endpointRegistry;
	/** @var Transaction */
	private $transaction;

	/**
	 * @return Transaction
	 */
	public function getTransaction()
	{
		return $this->transaction;
	}

	/**
	 * Returns the endpoint registry.
	 * @return EndpointRegistry
	 */
	public function getEndpointRegistry()
	{
		return $this->endpointRegistry;
	}

	/**
	 * @param EndpointRegistry $endpointRegistry
	 */
	public function setEndpointRegistry(EndpointRegistry $endpointRegistry)
	{
		$this->endpointRegistry = $endpointRegistry;
	}

	/**
	 * @param Transaction $transaction
	 */
	public function setTransaction(Transaction $transaction)
	{
		$this->transaction = $transaction;
	}
}