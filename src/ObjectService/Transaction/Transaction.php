<?php

namespace Light\ObjectService\Transaction;

use Light\Exception\Exception;
use Light\Exception\InvalidParameterType;

/**
 * A transaction encompassing changes to objects.
 *
 */
class Transaction
{
	private $open	= false;
	private $closed = false;
	
	protected $dirtyObjects = array();

	/**
	 * Marks this object as modified in this transaction.
	 * @param object	$dirty
	 * @return \Light\ObjectService\Transaction\Transaction
	 */
	public function saveDirty($dirty)
	{
		if (!is_object($dirty))
		{
			throw new InvalidParameterType('$dirty', $dirty, "object");
		}
		if (!in_array($dirty, $this->dirtyObjects, true))
		{
			$this->dirtyObjects[] = $dirty;
		}
		return $this;
	}
	
	/**
	 * Returns a list of objects modified in this transaction.
	 * @return object[]
	 */
	public function getDirty()
	{
		return $this->dirtyObjects;
	}

	/**
	 * Returns true if the transaction has begun.
	 * @return boolean
	 */
	public function isOpen()
	{
		return $this->open;
	}
	
	public function begin()
	{
		if ($this->open)
		{
			throw new Exception("Transaction has already been started");
		}
		if ($this->closed)
		{
			throw new Exception("Transaction has been closed and cannot be reopened");
		}
		$this->open = true;
		
	}
	
	public function commit()
	{
		if (!$this->open)
		{
			throw new Exception("Transaction has not been started");
		}

		$this->open = false;
		$this->closed = true;
	}
	
	public function rollback()
	{
		if (!$this->open)
		{
			throw new Exception("Transaction has not been started");
		}

		$this->open = false;
		$this->closed = true;
	}
}