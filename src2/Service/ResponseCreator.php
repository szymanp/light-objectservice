<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedResource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Creates a HTTP Response based on a request and response resources.
 */
interface ResponseCreator
{
	/**
	 * Creates a new Response object.
	 * @param ResolvedResource $requestResource
	 * @param RequestResult    $requestResult
	 * @return Response
	 */
	public function newResponse(ResolvedResource $requestResource, RequestResult $requestResult);
}
