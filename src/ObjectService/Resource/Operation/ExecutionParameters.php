<?php
namespace Light\ObjectService\Resource\Operation;

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
	 * Returns the object registry.
	 * @return \Light\ObjectService\ObjectRegistry
	 */
	public function getObjectRegistry();
}
