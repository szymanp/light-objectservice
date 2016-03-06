<?php
namespace Light\ObjectService\Resource\Util;

use Light\ObjectAccess\Transaction\Transaction;
use Szyman\ObjectService\Service\ExecutionParameters;
use Szyman\ObjectService\Configuration\Endpoint;
use Light\ObjectService\Service\EndpointRegistry;

class DefaultExecutionParameters implements ExecutionParameters
{
	/** @var EndpointRegistry */
	private $endpointRegistry;
	/** @var \Szyman\ObjectService\Configuration\Endpoint */
	private $endpoint;
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

	/**
	 * @inheritdoc
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	/**
	 * Sets the endpoint associated with this request.
	 * @param Endpoint $endpoint
	 */
	public function setEndpoint(Endpoint $endpoint)
	{
		$this->endpoint = $endpoint;
	}

}