<?php
namespace Szyman\ObjectService\Configuration;

use Szyman\ObjectService\Service\RequestBodyType;

interface RequestBodyTypeMap
{
	/**
	 * Returns the request body type corresponding to the MIME content-type.
	 * @param string	$contentType
	 * @return RequestBodyType	A RequestBodyType, if a match is found; otherwise, NULL.
	 */
	public function getRequestBodyType($contentType);
}
