<?php
namespace Light\ObjectService\Service\Util;

use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Service\InvocationParameters;
use Light\ObjectService\Transaction\Transaction;

/**
 * A configuration class for the ObjectService.
 *
 */
class InvocationParametersObject implements InvocationParameters
{
	/** @var \Light\ObjectService\ObjectRegistry */
	private $objectRegistry;
	/** @var \Light\ObjectService\Transaction\Transaction */
	private $transaction;
	
	/**
	 * Sets the object registry to be used with the REST service.
	 * @param ObjectRegistry $objectRegistry
	 * @return \Light\ObjectService\Service\Util\InvocationParametersObject
	 */
	public function setObjectRegistry(ObjectRegistry $objectRegistry)
	{
		$this->objectRegistry = $objectRegistry;
		return $this;
	}

	public function setTransaction(Transaction $tx)
	{
		$this->transaction = $tx;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Resource\Operation\ExecutionParameters::getObjectRegistry()
	 */
	public function getObjectRegistry()
	{
		return $this->objectRegistry;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Resource\Operation\ExecutionParameters::getTransaction()
	 */
	public function getTransaction()
	{
		return $this->transaction;
	}
}