<?php
namespace Light\ObjectService\Resource\Operation;
use Light\ObjectService\EndpointRegistry;

/**
 * Parameters for the execution of an operation.
 */
interface ExecutionParameters
{
	/**
	 * @return \Light\ObjectService\Transaction\Transaction
	 */
	public function getTransaction();
	
	/**
	 * Returns the endpoint registry.
	 * @return EndpointRegistry
	 */
	public function getEndpointRegistry();
}
