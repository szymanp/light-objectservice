<?php
namespace Light\ObjectService\Resource\Util;

use Light\ObjectAccess\Transaction\Transaction;
use Szyman\ObjectService\Service\ExecutionEnvironment;
use Szyman\ObjectService\Configuration\Endpoint;
use Light\ObjectService\Service\EndpointRegistry;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class DefaultExecutionEnvironment implements ExecutionEnvironment
{
	/** @var EndpointRegistry */
	private $endpointRegistry;
	/** @var \Szyman\ObjectService\Configuration\Endpoint */
	private $endpoint;
	/** @var Transaction */
	private $transaction;
    /** @var LoggerInterface */
    private $logger;
    
    public function __construct()
    {
        $this->logger = new NullLogger;
    }

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
    
    /** @inheritdoc */
    public function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * Sets the logger for use with this request.
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

}