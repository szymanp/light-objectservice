<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Exceptions\InvalidRequestException;
use Light\ObjectService\Service\Request\Request;
use Light\ObjectService\Service\Request\RequestObject;
use Light\Util\HTTP\Request as HTTPRequest;
use Light\Exception\Exception;
use Light\Exception\NotImplementedException;

class JsonRequestReader
{
	private static $validMethods = array("GET", "POST", "PUT", "DELETE", "ACTION", "TRANSACTION");
	
	private $basePath;
	
	public function __construct($basePath)
	{
		$this->basePath = $basePath;
	}
	
	/**
	 * Returns true if the HTTP Request can be processed by this Request Reader.
	 * @param HTTPRequest $httpRequest
	 * @return boolean
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
			return ($ct == "application/json");
		}
		
		return false;
	}
	
	/**
	 * Parse a HTTP Request and return a Service Request object. 
	 * @param HTTPRequest $httpRequest
	 * @throws InvalidRequestException
	 * @return \Light\ObjectService\Service\Request\Request
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
		$requestObject->setResourcePath($this->readPath($httpRequest, $body));

		$operationReader = JsonOperationReader::createRoot($method, $requestObject->getResourcePath());
		$requestObject->setOperation($operationReader->read());
		
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
	
	/**
	 * Reads the PathExpression for this request.
	 * @param HTTPRequest 	$httpRequest
	 * @param \stdClass 	$body
	 * @return \Light\ObjectService\Expression\PathExpression
	 */
	private function readPath(HTTPRequest $httpRequest, \stdClass $body = null)
	{
		$basePath = $this->basePath;
		
		$currentUri = $httpRequest->getUri();
		if (substr($currentUri, 0, strlen($basePath)) == $basePath)
		{
			$href = substr($currentUri, strlen($basePath));
		}
		else
		{
			throw new Exception("Request URI <%1> is not within base path scope <%2>", $currentUri, $path);
		}
		
		if ($body && $body->query)
		{
			if (is_object($body->query))
			{
				$whereExprs = $body->query;
			}
			else
			{
				throw new InvalidRequestException("body.query must be a JSON object");
			}
		}
		else
		{
			$whereExprs = null;
		}
		
		$pathExpressionReader = new JsonPathExpressionReader($href, $whereExprs);
		return $pathExpressionReader->readPathExpression();
	}
	
	private function readSelection(\stdClass $data)
	{
		
	}
}