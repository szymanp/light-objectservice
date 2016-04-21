<?php
namespace Szyman\ObjectService\Service;

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
	 * @param DetailedExecutionEnvironment  $environment
	 * @param RequestResult                 $requestResult
	 * @throws \Exception
	 */
	public function handle(DetailedExecutionEnvironment $environment, RequestResult $requestResult)
	{
		$environment->getTransaction()->transfer();
	}
}
