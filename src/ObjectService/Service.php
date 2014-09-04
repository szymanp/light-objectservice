<?php 
namespace Light\ObjectService;

use Light\ObjectService\Service\Invocation;
use Light\ObjectService\Service\InvocationParameters;
use Light\ObjectService\Service\Request\RequestReader;
use Light\ObjectService\Service\Response\Response;
use Light\Util\HTTP\Request as HTTPRequest;
use Light\Util\HTTP\Response as HTTPResponse;
use Negotiation\FormatNegotiator;

class Service
{
	/** @var \Light\ObjectService\Service\InvocationParameters */
	private $invocationParameters;
	
	/** @var \Light\Util\HTTP\Request */
	private $httpRequest;
	/** @var \Light\Util\HTTP\Response */
	private $httpResponse;
	
	/** @var \Light\ObjectService\Service\Request\RequestReader[] */
	private $requestReaders = array();
	
	/** @var \Light\ObjectService\Service\Response\Response[] */
	private $responses = array();
	
	/** @var array<string, \Light\ObjectService\Service\Request\RequestReader[]> */
	private $inputTypes = array();
	/** @var array<string, \Light\ObjectService\Service\Response\Response[]> */
	private $outputTypes = array();
	
	private static $errorExceptionConfigured = false;
	
	public function __construct(InvocationParameters $parameters, HTTPRequest $request = null, HTTPResponse $response = null)
	{
		$this->invocationParameters = $parameters;
		$this->httpRequest  = $request ? $request : new HTTPRequest();
		$this->httpResponse = $response ? $response : new HTTPResponse();
		$this->setupErrorException();
	}
	
	/**
	 * Adds a new RequestReader.
	 * @param RequestReader $requestReader
	 * @return \Light\ObjectService\Service	Fluent API.
	 */
	public function addRequestReader(RequestReader $requestReader)
	{
		$this->requestReaders[] = $requestReader;

		$contentTypes = $requestReader->getAcceptableContentTypes();
		foreach($contentTypes as $contentType)
		{
			$this->inputTypes[strtolower($contentType)][] = $requestReader;
		}
		
		return $this;
	}
	
	/**
	 * Adds a new Response.
	 * @param Response $response
	 * @return \Light\ObjectService\Service	Fluent API.
	 */
	public function addResponse(Response $response)
	{
		$this->responses[] = $response;
		
		$contentType = $response->getContentType();
		$this->outputTypes[$contentType][] = $response;
		
		return $this;
	}
	
	/**
	 * Execute the service. 
	 */
	public function invoke()
	{
		$requestReader = $this->pickRequestReader();
		$response	   = $this->pickResponse();
		
		if (!$response)
		{
			$this->replyNotAcceptable();
		}
		else if (!$requestReader)
		{
			$this->replyUnsupportedMediaType();
		}
		else
		{
			$this->invokeRequest($requestReader, $response);
		}
	}
	
	private function invokeRequest(RequestReader $requestReader, Response $response)
	{
		try
		{
			$request = $requestReader->read($this->httpRequest);
		}
		catch (\Exception $e)
		{
			$response->sendInternalError($e);
			return;
		}
		
		try
		{
			$invocation = new Invocation($this->invocationParameters, $request, $response);
			$invocation->invoke();
		}
		catch (\Exception $e)
		{
			$response->sendInternalError($e);
			return;
		}
	}
	
	/**
	 * @return \Light\ObjectService\Service\Request\RequestReader
	 */
	private function pickRequestReader()
	{
		$contentType = $this->httpRequest->getHeader("content-type");
		
		if (!$contentType)
		{
			return empty($this->requestReaders) ? null : $this->requestReaders[0];
		}
		
		$contentType = strtolower($contentType);
		$readers = empty($this->inputTypes) ? null : $this->inputTypes[$contentType];
		if (!$readers)
		{
			return null;
		}
		
		foreach($this->inputTypes[$contentType] as $reader)
		{
			if ($reader->isAcceptable($this->httpRequest))
			{
				return $reader;
			}
		}
		
		return null;
	}
	
	/**
	 * @return \Light\ObjectService\Service\Response\Response
	 */
	private function pickResponse()
	{
		$availableOutputTypes = array_keys($this->outputTypes);
		
		$acceptHeader = $this->httpRequest->getHeader("accept");
		if (is_null($acceptHeader))
		{
			// Return the default response object as no Accept header was specified.
			return empty($this->responses) ? null : $this->responses[0];
		}
		else
		{
			// Return a negotiated response object
			$formatNegotiator = new FormatNegotiator();
			$selected = $formatNegotiator->getBest($acceptHeader, $availableOutputTypes);
			
			if (!$selected)
			{
				return null;
			}
			
			$contentType = $selected->getValue();
			$responses = @ $this->outputTypes[$contentType];
			if (!$responses)
			{
				return null;
			} 
			else
			{
				// Return the first Response object for this content type.
				return $responses[0];
			}
		}
	}
	
	private function replyNotAcceptable()
	{
		$body = "This service accepts the following content-types: ";
		$body .= implode(", ", array_keys($this->inputTypes));
		
		$this->httpResponse->sendStatus(406);
		$this->httpResponse->sendBody($body);
	}
	
	private function replyUnsupportedMediaType()
	{
		$body = "This service can produce the following content-types: ";
		$body .= implode(", ", array_keys($this->outputTypes));
		
		$this->httpResponse->sendStatus(415);
		$this->httpResponse->sendBody($body);
	}
	
	private function setupErrorException()
	{
		if (self::$errorExceptionConfigured) return;
		
		set_error_handler(function ($errno, $errstr, $errfile, $errline)
		{
			if ($errno == E_USER_ERROR || $errno == E_RECOVERABLE_ERROR)
			{
				throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
			}
		});
		
		self::$errorExceptionConfigured = true;
	}
}
