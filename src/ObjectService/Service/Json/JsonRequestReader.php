<?php
namespace Light\ObjectService\Service\Json;

use Light\Exception\Exception;
use Light\ObjectService\Exceptions\InvalidRequestException;
use Light\ObjectService\Service\Request\RequestObject;
use Light\ObjectService\Service\Request\RequestReader;
use Light\Util\HTTP\Request as HTTPRequest;

class JsonRequestReader implements RequestReader
{
	private static $validMethods = array("GET", "POST", "PUT", "DELETE", "ACTION", "TRANSACTION");
	
	private $basePath;
	
	public function __construct($basePath)
	{
		$this->basePath = $basePath;
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
		$requestObject->setResourcePath($this->readPath($httpRequest, $body));

		$operationReader = JsonOperationReader::createRoot($method,
														   $requestObject->getResourcePath(), 
														   $body && $body->data ? $body->data : null, 
														   $body && $body->meta ? $body->meta : null);
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
			throw new Exception("Request URI <%1> is not within base path scope <%2>", $currentUri, $this->basePath);
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