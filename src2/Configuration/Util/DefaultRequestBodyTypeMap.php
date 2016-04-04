<?php
namespace Szyman\ObjectService\Configuration\Util;

use Szyman\Exception\InvalidArgumentException;
use Szyman\ObjectService\Configuration\RequestBodyTypeMap;
use Szyman\ObjectService\Service\RequestBodyType;

final class DefaultRequestBodyTypeMap implements RequestBodyTypeMap, \ArrayAccess
{
	/** @var string[] */
	private $map = array();
	
	public function offsetGet($offset)
	{
		return @ $this->map[$offset];
	}
	
	public function offsetExists($offset)
	{
		return isset($this->map[$offset]);
	}
	
	public function offsetUnset($contentType)
	{
		if (!is_string($contentType))
		{
			throw InvalidArgumentException::newInvalidType('$contentType', $contentType, "string");
		}
		unset($this->map[$contentType]);
	}
	
	public function offsetSet($contentType, $requestBodyType)
	{
		if (!is_string($contentType))
		{
			throw InvalidArgumentException::newInvalidType('$contentType', $contentType, "string");
		}
		if (!($requestBodyType instanceof RequestBodyType))
		{
			throw InvalidArgumentException::newInvalidType('$requestBodyType', $requestBodyType, RequestBodyType::class);
		}
		$this->map[$contentType] = $requestBodyType;
	}
	
	/**
	 * Returns the request body type corresponding to the MIME content-type.
	 * @param string	$contentType
	 * @return RequestBodyType	A RequestBodyType, if a match is found; otherwise, NULL.
	 */
	public function getRequestBodyType($contentType)
	{
		if (isset($this->map[$contentType]))
		{
			return $this->map[$contentType];
		}
		else
		{
			return null;
		}
	}
}
