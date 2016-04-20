<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Transaction\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Szyman\ObjectService\Configuration\Configuration;

/**
 * Helps <kbd>RequestProcessor</kbd> in handling transactions.
 *
 * Instances of this class are used by {@link RequestProcessor} to validate and possibly
 * modify an ongoing transaction after a request has been handled by a {@link RequestHandler}
 * and before a response is created by a {@link ResponseCreator}.
 *
 * The default implementation of this class simply calls <kbd>$transaction->transfer()</kbd>.
 *
 */
class TransactionHandler
{
	/**
	 * Handle an ongoing transaction.
	 * @param Configuration		$conf
	 * @param Request			$request
	 * @param RequestComponents	$requestComponents
	 * @param Transaction		$transaction
	 * @throws \Exception
	 */
	public function handle(Configuration $conf, Request $request, RequestComponents $requestComponents, Transaction $transaction)
	{
		$transaction->transfer();
	}
}
