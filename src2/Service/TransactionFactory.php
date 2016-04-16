<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Transaction\Transaction;

interface TransactionFactory
{
	/**
	 * Returns a new transaction.
	 * @return Transaction
	 */
	public function newTransaction();
}
