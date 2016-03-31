<?php
namespace Szyman\ObjectService\Service;

use Symfony\Component\HttpFoundation\Request;
use Light\ObjectAccess\Resource\Addressing\ResourceAddress;
use Light\ObjectAccess\Resource\ResolvedResource;

/**
 * An interface for classes that can handle REST requests.
 */
interface RequestHandler
{
	/**
	 * Handle a request.
	 *
	 * @param Request					$request
	 * @param ResourceAddress			$resourceAddress	The address corresponding to the request-uri.
	 * @param ResolvedResource			$subjectResource	The resource that is the subject of this request.
	 *														This might be the resource identified by the <kbd>$resourceAddress</kbd>,
	 *														but in some cases (like a Creation request using the PUT method) these might be different.
	 * @param RequestBodyDeserializer	$deserializer		A deserializer capable of reading the body of the <kbd>$request</kbd>, if any.
	 * @return RequestResult
	 */
	public function handle(Request $request, ResourceAddress $resourceAddress, ResolvedResource $subjectResource, RequestBodyDeserializer $deserializer = NULL);
}
