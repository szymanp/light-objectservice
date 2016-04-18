<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedValue;
use Szyman\Exception\InvalidArgumentTypeException;
use Szyman\Exception\InvalidArgumentValueException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Response\DataSerializer;

/**
 * A map that returns a content-type based on the PHP type of the resource and the serializer format.
 */
final class TypeBasedResponseContentTypeMap implements ResponseContentTypeMap
{
	private $types = array();
	private $classes = array();

	/**
	 * Adds a new mapping for a PHP primitive type.
	 * @param string	$type			The type of the value underlying the resource, e.g. "string".
	 * @param string	$formatName		The name of the data serializer format.
	 * @param string	$contentType	The MIME content-type.
	 * @return $this
	 */
	public function addPrimitiveType($type, $formatName, $contentType)
	{
		$this->validate($type, $formatName, $contentType);
		$this->types[$type][$formatName] = $contentType;
		return $this;
	}

	/**
	 * Adds a new mapping for a class.
	 * Note that an object of a subclasses will match a superclass that was added to the map.
	 * The mappings that are added first are also checked first, therefore most specific entries should be added first,
	 * while more generic ones last.
	 * @param string	$type			The type of the value underlying the resource, i.e. a name of a PHP class.
	 * @param string	$formatName		The name of the data serializer format.
	 * @param string	$contentType	The MIME content-type.
	 * @return $this
	 */
	public function addClass($type, $formatName, $contentType)
	{
		$this->classes[$type][$formatName] = $contentType;
		return $this;
	}

	private function validate($type, $formatName, $contentType)
	{
		if (!is_string($contentType))
		{
			throw new InvalidArgumentTypeException('$contentType', $contentType, 'string');
		}
		if (strpos($contentType, '/') === false)
		{
			throw new InvalidArgumentValueException('$contentType', $contentType, 'Invalid MIME type');
		}
		if (!is_string($type))
		{
			throw new InvalidArgumentTypeException('$type', $type, 'string');
		}
		if (!is_string($formatName))
		{
			throw new InvalidArgumentTypeException('$formatName', $formatName, 'string');
		}
	}

	public function getContentType(ResolvedResource $resource, DataSerializer $serializer)
	{
		if ($resource instanceof ResolvedValue)
		{
			$value = $resource->getValue();

			if (is_object($value))
			{
				foreach($this->classes as $type => $values)
				{
					if ($value instanceof $type)
					{
						if (isset($values[$serializer->getFormatName()]))
						{
							return $values[$serializer->getFormatName()];
						}
					}
				}
			}
			else
			{
				foreach($this->types as $type => $values)
				{
					if (gettype($value) == $type)
					{
						if (isset($values[$serializer->getFormatName()]))
						{
							return $values[$serializer->getFormatName()];
						}
					}
				}
			}
		}
		return null;
	}

}