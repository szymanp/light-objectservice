<?php
namespace Light\ObjectService\Service;

use Light\Exception\Exception;
use Light\ObjectAccess\Transaction\Util\DummyTransaction;
use Light\ObjectService\Exception\MalformedRequest;
use Light\ObjectService\Resource\Util\DefaultExecutionParameters;
use Symfony\Component\HttpFoundation;

class EndpointContainer
{
	/** @var EndpointRegistry */
	private $endpointRegistry;
	/** @var HttpFoundation\Request */
	private $httpRequest;
	/** @var array<string, RequestReader[]> */
	private $requestReaders = array();
	/** @var array<string, ResponseFactory[]> */
	private $responseFactories = array();
	/** @var RequestReader */
	private $primaryRequestReader;
	/** @var \Closure */
	private $httpResponseFactory;

	public function __construct(EndpointRegistry $endpointRegistry)
	{
		$this->endpointRegistry = $endpointRegistry;
		$this->httpResponseFactory = function($content, $code, array $headers = array())
		{
			return new HttpFoundation\Response($content, $code, $headers);
		};
	}

	/**
	 * Adds a request reader to this container.
	 * @param RequestReader $requestReader
	 * @return $this
	 * @throws Exception
	 */
	public function addRequestReader(RequestReader $requestReader)
	{
		if (is_null($this->primaryRequestReader))
		{
			$this->primaryRequestReader = $requestReader;
		}
		foreach($requestReader->getAcceptableContentTypes() as $ct)
		{
			$ct = strtolower($ct);
			$this->requestReaders[$ct][] = $requestReader;
		}
		return $this;
	}

	/**
	 * Adds a response factory to this container.
	 * @param ResponseFactory $responseFactory
	 * @return $this
	 * @throws Exception
	 */
	public function addResponseFactory(ResponseFactory $responseFactory)
	{
		foreach($responseFactory->getContentTypes() as $ct)
		{
			$ct = strtolower($ct);
			$this->responseFactories[$ct][] = $responseFactory;
		}
		return $this;
	}

	/**
	 * Sets the primary request reader.
	 * The primary request reader will be used to service request that do not have any content type set
	 * (for example, GET request).
	 * By default, the first request reader added using {@link addRequestReader} will become the primary
	 * request reader.
	 * @param RequestReader $primaryRequestReader
	 * @return $this
	 */
	public function setPrimaryRequestReader($primaryRequestReader)
	{
		$this->primaryRequestReader = $primaryRequestReader;
		return $this;
	}

	/**
	 * Sets the HTTP request to be processed.
	 * @param HttpFoundation\Request $httpRequest
	 * @return $this
	 */
	public function setHttpRequest(HttpFoundation\Request $httpRequest)
	{
		$this->httpRequest = $httpRequest;
		return $this;
	}

	/**
	 * Sets the HTTP response factory to be used for reporting uncaught errors.
	 * @param callable $httpResponseFactory
	 * @return $this
	 */
	public function setHttpResponseFactory($httpResponseFactory)
	{
		$this->httpResponseFactory = $httpResponseFactory;
		return $this;
	}

	public function run()
	{
		if (is_null($this->httpRequest))
		{
			$this->httpRequest = HttpFoundation\Request::createFromGlobals();
		}

		// FIXME Where should the transaction come from?
		$transaction = new DummyTransaction();

		$requestReader = null;
		$response = null;

		try
		{
			$requestReader = $this->pickRequestReader();
			if (is_null($requestReader))
			{
				throw new EndpointContainer_Exception(
					"The container is not configured to handle requests of this type",
					HttpFoundation\Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
			}

			$responseFactory = $this->pickResponseFactory();
			if (is_null($responseFactory))
			{
				throw new EndpointContainer_Exception(
					"The container is unable to provide a response in any of the requested types",
					HttpFoundation\Response::HTTP_NOT_ACCEPTABLE);
			}

			$response = $responseFactory->getResponse();
		}
		catch (EndpointContainer_Exception $e)
		{
			$this->sendErrorResponse($e);
		}
		catch (\Exception $e)
		{
			$this->sendErrorResponse(new EndpointContainer_Exception(
				"The container encountered a problem when reading the request",
				HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $e));
		}

		if (is_null($requestReader) or is_null($response))
		{
			return;
		}

		try
		{
			$request = $requestReader->read($this->httpRequest, $this->endpointRegistry, $transaction);

			$executionParameters = new DefaultExecutionParameters();
			$executionParameters->setEndpoint($request->getResourceAddress()->getEndpoint());
			$executionParameters->setTransaction($transaction);
			$executionParameters->setEndpointRegistry($this->endpointRegistry);

			$requestProcessor = new RequestProcessor($executionParameters, $request, $response);
			$requestProcessor->process();
		}
		catch (\Exception $e)
		{
			try
			{
				$response->setException($e);
				$response->send();
			}
			catch (\Exception $e2)
			{
				$this->sendErrorResponse(new EndpointContainer_Exception(
					"The container encountered a problem when sending an error response",
					HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,
					$e2));
			}
		}
	}

	/**
	 * Finds a request reader that can service the current HTTP request.
	 * @return RequestReader	A request reader, if a matching one is found; otherwise, NULL.
	 */
	protected function pickRequestReader()
	{
		$contentType = $this->httpRequest->getContentType();

		if (is_null($contentType))
		{
			return $this->primaryRequestReader;
		}

		$contentType = strtolower($contentType);
		if (!isset($this->requestReaders[$contentType]))
		{
			// TOD Should we have a fallback reader?
			return null;
		}

		foreach($this->requestReaders[$contentType] as $requestReader)
		{
			if ($requestReader->isAcceptable($this->httpRequest))
			{
				return $requestReader;
			}
		}

		return null;
	}

	/**
	 * Finds a response factory that can service the current HTTP request.
	 * @return ResponseFactory	A response factory, if a matching one is found; otherwise, NULL.
	 */
	protected function pickResponseFactory()
	{
		$contentTypes = $this->httpRequest->getAcceptableContentTypes();

		if (empty($contentTypes))
		{
			return null;
		}

		foreach($contentTypes as $contentType)
		{
			$contentType = strtolower($contentType);
			if (!isset($this->responseFactories[$contentType]))
			{
				continue;
			}

			foreach($this->responseFactories[$contentType] as $responseFactory)
			{
				if ($responseFactory->isAcceptable($this->httpRequest))
				{
					return $responseFactory;
				}
			}
		}

		return null;
	}

	protected function sendErrorResponse(EndpointContainer_Exception $e)
	{
		$content = <<<EOT
<html>
<body>
	<p>{$e->getMessage()}</p>
</body>
</html>
EOT;


		$httpResponse = call_user_func($this->httpResponseFactory, $content, $e->getCode());
		$httpResponse->setCharset("UTF-8");
		$httpResponse->headers->set("content-type", array("text/html"));
		$httpResponse->prepare($this->httpRequest);
		$httpResponse->send();
	}

}

final class EndpointContainer_Exception extends \Exception
{
}