<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Exception\UnsupportedMediaType;
use Light\ObjectService\Resource\Util\DefaultExecutionEnvironment;
use Symfony\Component\HttpFoundation\Request;
use Szyman\Exception\UnexpectedValueException;
use Szyman\ObjectService\Configuration\Configuration;

final class RequestProcessor
{
	/** @var Configuration */
	private $conf;

	public function __construct(Configuration $conf)
	{
		$this->conf = $conf;
	}

	/**
	 * Handles the request.
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request)
	{
		try
		{
			// Read the request
			$requestReader = new RestRequestReader($this->conf);
			$requestComponents = $requestReader->readRequest($request);

			try
			{
				return $this->internalHandle($request, $requestComponents);
			}
			catch (\Exception $e)
			{
				// The exception occurred while handling the request, but after decoding it.
				// Try to send the exception using a suitable ResponseCreator.
				return $this->createResponse($request, new ExceptionRequestResult($e), $requestComponents);
			}
		}
		catch (\Exception $e)
		{
			// The exception occurred while decoding the request, or when creating the exception response.
			return $this->createResponse($request, new ExceptionRequestResult($e));
		}
	}

	/**
	 * @param Request           $request
	 * @param RequestComponents $requestComponents
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	private function internalHandle(Request $request, RequestComponents $requestComponents)
	{
		// Setup the transaction
		$transaction = $this->conf->getTransactionFactory()->newTransaction();
		if (!($transaction instanceof Transaction))
		{
			throw UnexpectedValueException::newInvalidReturnValue($this->conf->getTransactionFactory(), 'newTransaction', $transaction, 'Expecting Transaction');
		}

		// Build the execution environment
		$environment = new DefaultExecutionEnvironment();
		$environment->setEndpointRegistry($this->conf->getEndpointRegistry());
		$environment->setTransaction($transaction);
		$environment->setEndpoint($requestComponents->getEndpointAddress()->getEndpoint());

		// Invoke request handler
		$requestHandler = $this->conf->getRequestHandlerFactory()->newRequestHandler($requestComponents->getRequestType(), $environment);
		if (!($requestHandler instanceof RequestHandler))
		{
			throw UnexpectedValueException::newInvalidReturnValue($this->conf->getRequestHandlerFactory(), 'newRequestHandler', $requestHandler, 'Expecting RequestHandler');
		}
		$requestResult = $requestHandler->handle($request, $requestComponents);

		if (!($requestResult instanceof RequestResult))
		{
			throw UnexpectedValueException::newInvalidReturnValue($requestHandler, 'handle', $requestResult, 'Expecting RequestResult');
		}

		// Invoke the response creator and return the response.
		return $this->createResponse($request, $requestResult, $requestComponents);
	}

	/**
	 * @param Request           $request
	 * @param RequestResult     $requestResult
	 * @param RequestComponents $requestComponents
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	private function createResponse(Request $request, RequestResult $requestResult, RequestComponents $requestComponents = null)
	{
		$responseCreator = $this->conf->getResponseCreatorFactory()->newResponseCreator($request, $requestResult, $requestComponents);
		if (is_null($responseCreator))
		{
			throw new UnsupportedMediaType(join(", ", $request->getAcceptableContentTypes()), "Could not find a matching response creator");
		}
		if (!($responseCreator instanceof ResponseCreator))
		{
			throw UnexpectedValueException::newInvalidReturnValue(
				$this->conf->getResponseCreatorFactory(),
				'newResponseCreator',
				$responseCreator,
				'Expecting ResponseCreator');
		}
		return $responseCreator->newResponse($request, $requestResult, $requestComponents);
	}

}