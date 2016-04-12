<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Resource\ResolvedResource;
use Szyman\Exception\InvalidArgumentException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Response\DataSerializer;

class DefaultResponseContentTypeMap implements ResponseContentTypeMap
{
	/**
	 * @var string[]
	 */
	private $map = array();

	/**
	 * Adds a new content-type mapping.
	 * @param ResolvedResource	$resource
	 * @param DataSerializer	$serializer
	 * @return $this
	 */
	public function set(ResolvedResource $resource, DataSerializer $serializer, $contentType)
	{
		if (!is_string($contentType))
		{
			throw InvalidArgumentException::newInvalidType('$contentType', $contentType, 'string');
		}
		if (strpos($contentType, '/') === false)
		{
			throw InvalidArgumentException::newInvalidValue('$contentType', $contentType, 'Invalid MIME type');
		}
		$this->map[$this->getKey($resource, $serializer)] = $contentType;
		return $this;
	}

	/**
	 * Returns the MIME content-type for the response body.
	 * @param ResolvedResource	$resource	The resource being transmitted.
	 *										Note that in addition to "application" resources,
	 *										a resource containing an <kbd>Exception</kbd> could be passed as well.
	 * @param DataSerializer	$serializer	The <kbd>DataSerializer</kbd> used to convert the resource to a a standard
	 *										format such as XML or JSON. The {@link DataSerializer::getFormatName()}
	 *										method can be used to get the name of the format.
	 * @return string	A MIME content-type for this resource, if the map recognizes this resource;
	 *					otherwise, NULL.
	 */
	public function getContentType(ResolvedResource $resource, DataSerializer $serializer)
	{
		$key = $this->getKey($resource, $serializer);
		if (isset($this->map[$key]))
		{
			return $this->map[$key];
		}
		return null;
	}
	
	protected function getKey(ResolvedResource $resource, DataSerializer $serializer)
	{
		return spl_object_hash($resource) . '-' . spl_object_hash($serializer);
	}
}
