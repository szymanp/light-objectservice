<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectAccess\Transaction\Util\DummyTransaction;
use Szyman\ObjectService\Service\TransactionFactory;

class DummyTransactionFactory implements TransactionFactory
{
	/**
	 * Returns a new transaction.
	 * @return Transaction
	 */
	public function newTransaction()
	{
		return new DummyTransaction();
	}
}