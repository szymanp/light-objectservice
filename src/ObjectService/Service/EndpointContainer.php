<?php
namespace Light\ObjectService\Service;

use Light\Exception\Exception;
use Light\ObjectAccess\Transaction\Util\DummyTransaction;
use Light\ObjectService\Formats\Html\HtmlExceptionSerializer;
use Light\ObjectService\Resource\Util\DefaultExecutionParameters;
use Light\ObjectService\Service\Protocol\ExceptionSerializer;
use Light\ObjectService\Service\Protocol\Protocol;
use Symfony\Component\HttpFoundation;

class EndpointContainer
{
	/** @var EndpointRegistry */
	private $endpointRegistry;
	/** @var HttpFoundation\Request */
	private $httpRequest;
	/** @var Protocol[] */
	private $protocols = array();
	/** @var \Closure */
	private $httpResponseFactory;
	/** @var ExceptionSerializer */
	private $defaultExceptionSerializer;
	/** @var boolean */
	private $production = true;

	public function __construct(EndpointRegistry $endpointRegistry)
	{
		$this->endpointRegistry = $endpointRegistry;
		$this->httpResponseFactory = function($content, $code, array $headers = array())
		{
			return new HttpFoundation\Response($content, $code, $headers);
		};
		$this->defaultExceptionSerializer = new HtmlExceptionSerializer();
	}

	/**
	 * Adds a protocol to this container.
	 * @param Protocol $protocol
	 * @return $this
	 * @throws Exception
	 */
	public function addProtocol(Protocol $protocol)
	{
		$this->protocols[] = $protocol;
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
	 * Sets the production mode.
	 * If production mode is disabled, then detailed information about uncaught exceptions is printed.
	 * @param boolean $production
	 */
	public function setProduction($production)
	{
		$this->production = $production;
	}

	/**
	 * Sets the exception serializer that will be used if no other serializer matches the requested content-type.
	 * @param ExceptionSerializer $defaultExceptionSerializer
	 */
	public function setDefaultExceptionSerializer(ExceptionSerializer $defaultExceptionSerializer)
	{
		$this->defaultExceptionSerializer = $defaultExceptionSerializer;
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

		$protocolInstance = null;

		try
		{
			$protocol = $this->pickProtocolOrThrow();
			$protocol->configure($this->endpointRegistry);
			$protocolInstance = $protocol->newInstance($this->httpRequest, $transaction);
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

		if (is_null($protocolInstance))
		{
			return;
		}

		$httpResponse = null;

		try
		{
			$request = $protocolInstance->readRequest();

			$executionParameters = new DefaultExecutionParameters();
			$executionParameters->setEndpoint($request->getResourceAddress()->getEndpoint());
			$executionParameters->setTransaction($transaction);
			$executionParameters->setEndpointRegistry($this->endpointRegistry);

			$requestProcessor = new RequestProcessor($executionParameters, $request);
			$requestProcessor->process();

			if ($requestProcessor->hasEntity())
			{
				$httpResponse = $protocolInstance->prepareResourceResponse($requestProcessor->getEntity());
			}
			else if ($requestProcessor->hasException())
			{
				$httpResponse = $protocolInstance->prepareExceptionResponse($requestProcessor->getException());
			}
			else
			{
				throw new \LogicException("RequestProcessor returned no entity or exception");
			}
		}
		catch (\Exception $e)
		{
			try
			{
				$httpResponse = $protocolInstance->prepareExceptionResponse($e);
			}
			catch (\Exception $e2)
			{
				$this->sendErrorResponse(new EndpointContainer_Exception(
					"The container encountered a problem when sending an error response",
					HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,
					$e2));
				return;
			}
		}

		if ($httpResponse)
		{
			$httpResponse->prepare($this->httpRequest);
			$httpResponse->send();
		}
		else
		{
			$this->sendErrorResponse(new EndpointContainer_Exception(
				"The protocol did not produce any response",
				HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR));
		}
	}

	/**
	 * Returns the first protocol that is capable of reading and responding to this request.
	 * @return Protocol
	 * @throws EndpointContainer_Exception
	 */
	protected function pickProtocolOrThrow()
	{
		$score = 0;
		$scoreResult = null;

		foreach($this->protocols as $protocol)
		{
			$result = $protocol->accepts($this->httpRequest);
			if ($result->accepted())
			{
				return $protocol;
			}
			elseif ($result->badRequestedContentType())
			{
				$scoreResult = $result;
				$score = 3;
			}
			elseif ($result->badSuppliedContentType() && $score < 2)
			{
				$scoreResult = $result;
				$score = 2;
			}
			elseif ($result->badMethod() && $score < 1)
			{
				$scoreResult = $result;
				$score = 1;
			}
		}

		if (is_null($scoreResult) || $scoreResult->badSuppliedContentType())
		{
			throw new EndpointContainer_Exception(
				"The container is not configured to handle requests of this type",
				HttpFoundation\Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
		}
		elseif ($scoreResult->badRequestedContentType())
		{
			throw new EndpointContainer_Exception(
				"The container is unable to provide a response to this request in any of the requested types",
				HttpFoundation\Response::HTTP_NOT_ACCEPTABLE);
		}
		elseif ($scoreResult->badMethod())
		{
			throw new EndpointContainer_Exception(
				"The container is unable to handle the requested method",
				HttpFoundation\Response::HTTP_BAD_REQUEST);
		}
	}

	protected function sendErrorResponse(EndpointContainer_Exception $e)
	{
		$content = $this->defaultExceptionSerializer->serialize($e);

		$httpResponse = call_user_func($this->httpResponseFactory, $content, $e->getCode());
		$httpResponse->setCharset("UTF-8");
		$httpResponse->headers->set("content-type", array($this->defaultExceptionSerializer->getContentType()));
		$httpResponse->prepare($this->httpRequest);
		$httpResponse->send();
	}

}

final class EndpointContainer_Exception extends \Exception
{
}