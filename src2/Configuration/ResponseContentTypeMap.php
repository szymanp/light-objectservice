<?php
namespace Szyman\ObjectService\Configuration;

use Light\ObjectAccess\Resource\ResolvedResource;
use Szyman\ObjectService\Response\DataSerializer;

interface ResponseContentTypeMap
{
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
	public function getContentType(ResolvedResource $resource, DataSerializer $serializer);
}
