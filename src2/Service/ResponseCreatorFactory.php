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
	 * @param Request           $request			The HTTP request for which a response is to be created.
	 * @param RequestResult     $requestResult		The content of the response.
	 * @param RequestComponents $requestComponents	Additional information about the request. In some cases
	 *                                              this information might not be available.
	 *
	 * @return ResponseCreator A <kbd>ResponseCreator</kbd> object, if the factory is capable of producing a creator
	 *                         for this request; otherwise, NULL.
	 */
	public function newResponseCreator(Request $request, RequestResult $requestResult, RequestComponents $requestComponents = null);
}
