<?php
namespace Szyman\ObjectService\Service;

/**
 * Helps <kbd>RequestProcessor</kbd> in handling transactions.
 *
 * Instances of this class are used by {@link RequestProcessor} to validate and possibly
 * modify an ongoing transaction or request result after a request has been handled by a {@link RequestHandler}
 * and before a response is created by a {@link ResponseCreator}.
 */
class TransactionHandler
{
	/**
	 * Handle an ongoing transaction.
	 *
	 * The default implementation of this method simply calls <kbd>$transaction->transfer()</kbd>.
	 *
	 * @param DetailedExecutionEnvironment  $environment
	 * @param RequestResult                 $requestResult
	 * @throws \Exception
	 */
	public function handleTransaction(DetailedExecutionEnvironment $environment, RequestResult $requestResult)
	{
		$environment->getTransaction()->transfer();
	}

	/**
	 * Transforms a RequestResult into another one.
	 * @param DetailedExecutionEnvironment $environment
	 * @param RequestResult                $requestResult
	 * @return RequestResult
	 */
	public function transformResult(DetailedExecutionEnvironment $environment, RequestResult $requestResult)
	{
		return $requestResult;
	}
}
