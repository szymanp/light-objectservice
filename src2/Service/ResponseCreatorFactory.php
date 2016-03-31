<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Type\Type;
use Symfony\Component\HttpFoundation\Request;

/**
 * A factory for objects that can create a HTTP response to a certain type of request.
 */
interface ResponseCreatorFactory
{
	/**
	 * Returns a new ResponseCreator for building a response to a request.
	 *
	 * @param Request 		$request				The HTTP request for which the response will be created.
	 * @param RequestType	$requestType			The type of the request (Read, Create, Modify, etc.)
	 * @param Type    		$subjectResourceType	The type of the resource at the request-uri.
	 *
	 * TODO: Can it return NULL? What does it throw?
	 *
	 * @return ResponseCreator
	 */
	public function newResponseCreator(Request $request, RequestType $requestType, Type $subjectResourceType);
}
