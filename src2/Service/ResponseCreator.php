<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectService\Exception\SerializationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Creates a HTTP Response based on a request and response resources.
 */
interface ResponseCreator
{
	/**
	 * Creates a new Response object.
	 * @param Request           $request			The HTTP request for which a response is being made.
	 * @param RequestResult     $requestResult		The content of the response.
	 * @param RequestComponents $requestComponents	Additional information about the request. In some cases
	 *                                              this information might not be available.
	 * @return Response
	 * @throws \InvalidArgumentException	Thrown if <kbd>$requestResult</kbd> is not of a supported type.
	 * @throws SerializationException		Thrown if a problem was encountered while creating the response body.
	 * @throws ConfigurationException		Thrown if a problem with the configuration prevents the creation of a response.
	 */
	public function newResponse(Request $request, RequestResult $requestResult, RequestComponents $requestComponents = null);
}
