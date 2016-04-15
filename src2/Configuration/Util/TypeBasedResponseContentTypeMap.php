<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedValue;
use Szyman\Exception\InvalidArgumentException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Response\DataSerializer;

/**
 * A map that returns a content-type based on the PHP type of the resource and the serializer format.
 */
final class TypeBasedResponseContentTypeMap implements ResponseContentTypeMap
{
	private $map = array();

	/**
	 * Sets a new mapping.
	 * @param string	$type			The type of the value underlying the resource. It can be a class name or a primitive type name.
	 * @param string	$formatName		The name of the data serializer format.
	 * @param string	$contentType	The MIME content-type.
	 * @return $this
	 */
	public function set($type, $formatName, $contentType)
	{
		if (!is_string($contentType))
		{
			throw InvalidArgumentException::newInvalidType('$contentType', $contentType, 'string');
		}
		if (strpos($contentType, '/') === false)
		{
			throw InvalidArgumentException::newInvalidValue('$contentType', $contentType, 'Invalid MIME type');
		}
		if (!is_string($type))
		{
			throw InvalidArgumentException::newInvalidType('$type', $type, 'string');
		}
		if (!is_string($formatName))
		{
			throw InvalidArgumentException::newInvalidType('$formatName', $formatName, 'string');
		}
		$this->map[$type][$formatName] = $contentType;
		return $this;
	}

	public function getContentType(ResolvedResource $resource, DataSerializer $serializer)
	{
		if ($resource instanceof ResolvedValue)
		{
			$value = $resource->getValue();
			$type = is_object($value) ? get_class($value) : gettype($value);

			if (isset($this->map[$type][$serializer->getFormatName()]))
			{
				return $this->map[$type][$serializer->getFormatName()];
			}
		}
		return null;
	}

}