<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Service\EndpointRegistry;

/**
 * Parameters for the execution of an operation.
 */
interface ExecutionParameters
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
}
