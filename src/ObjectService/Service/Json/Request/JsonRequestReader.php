<?php
namespace Light\ObjectService\Service\Json\Request;

use Light\Exception\Exception;
use Light\ObjectService\Exceptions\InvalidRequestException;
use Light\ObjectService\Resource\UrlResourceSpecification;
use Light\ObjectService\Service\Request\RequestObject;
use Light\ObjectService\Service\Request\RequestReader;
use Light\Util\HTTP\Request as HTTPRequest;

class JsonRequestReader implements RequestReader
{
	private static $validMethods = array("GET", "POST", "PUT", "DELETE", "ACTION", "TRANSACTION");

	public function __construct()
	{
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Request\RequestReader::isAcceptable()
	 */
	public function isAcceptable(HTTPRequest $httpRequest)
	{
		$method = $httpRequest->getMethod();
		if ($method == "GET")
		{
			return true;
		}
		
		if (in_array($method, self::$validMethods, true))
		{
			$ct = $httpRequest->getHeader("content-type");
			return in_array($ct, $this->getAcceptableContentTypes());
		}
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Request\RequestReader::getAcceptableContentTypes()
	 */
	public function getAcceptableContentTypes()
	{
		return array("application/json");
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Request\RequestReader::read()
	 */
	public function read(HTTPRequest $httpRequest)
	{
		$method = $httpRequest->getMethod();
		
		// Determine method
		if ($method == "POST" || $method == "PUT" || $method == "DELETE")
		{
			$body = $this->readBody($httpRequest);
			
			if ($body->method)
			{
				if ($method == "POST")
				{
					$method = $body->method;
					if (!in_array($method, self::$validMethods, true))
					{
						throw new InvalidRequestException("Overriding method \"%1\" is invalid", $method);
					}
				}
				else
				{
					throw new InvalidRequestException("Overriding method can only be specified for a HTTP POST method", $method);
				}
			}
		}
		else if ($method == "GET")
		{
			$body = null;
		}
		else
		{
			throw new InvalidRequestException("Invalid method \"%1\"", $method);
		}
		
		// Create a new request object.
		$requestObject = new RequestObject();

		$rootResourceSpecification = new UrlResourceSpecification();
		// TODO UrlResourceSpecification expects a full URL.
		//		Could there be valid cases where the service endpoint has a different URL than the base for resources?
		//		Should we then have come URL converter class that handles this?
		$rootResourceSpecification->setUrl("");
		$this->readQuery($body, $rootResourceSpecification);

		$requestObject->setResourceSpecification($rootResourceSpecification);


		// TODO
		/*
		$requestObject->setResourcePath($this->readPath($httpRequest, $body));

		$operationReader = JsonOperationReader::createRoot($method,
														   $requestObject->getResourcePath(), 
														   $body && $body->data ? $body->data : null, 
														   $body && $body->meta ? $body->meta : null);
		$requestObject->setOperation($operationReader->read());
		*/
		
		if ($body && $body->select)
		{
			if (is_object($body->select))
			{
				$requestObject->setSelection($this->readSelection($body->select));
			}
			else
			{
				throw new InvalidRequestException("\"select\" must be a JSON object");
			}
		}
		
		return $requestObject;
	}

	private function readBody(HTTPRequest $httpRequest)
	{
		$body = $httpRequest->getBody(true);
		if (empty($body))
		{
			throw new InvalidRequestException("Request body is empty");
		}
		
		$json = json_decode($body);
		if (!is_object($json))
		{
			throw new InvalidRequestException("Request body is not a JSON object");
		}
		
		return $json;
	}
	
	private function readQuery(\stdClass $body = null, UrlResourceSpecification $resourceSpec)
	{
		if ($body && $body->query)
		{
			if (is_object($body->query))
			{
				foreach($body->query as $name => $query)
				{
					$resourceSpec->addQuery($name, JsonWhereExpressionSource::create($query));
				}
			}
			else
			{
				throw new InvalidRequestException("body.query must be a JSON object");
			}
		}
	}
	
	private function readSelection(\stdClass $data)
	{
		// TODO
	}
}