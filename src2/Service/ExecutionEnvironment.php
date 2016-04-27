<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Transaction\Transaction;
use Szyman\ObjectService\Configuration\Endpoint;
use Light\ObjectService\Service\EndpointRegistry;
use Psr\Log\LoggerInterface;

/**
 * Provides access to objects relevant to the execution of a service request.
 */
interface ExecutionEnvironment
{
	/**
	 * @return Transaction
	 */
	public function getTransaction();
	
	/**
	 * Returns the endpoint registry.
	 * @return EndpointRegistry
	 */
	public function getEndpointRegistry();

	/**
	 * Returns the endpoint associated with this request.
	 * @return Endpoint
	 */
	public function getEndpoint();
    
    /**
     * Returns a logger for use in the execution of the request.
     * @return LoggerInterface
     */
    public function getLogger();
}
