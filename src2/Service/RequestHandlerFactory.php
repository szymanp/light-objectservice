<?php
namespace Szyman\ObjectService\Service;

/**
 * A factory for objects that can handle a REST request.
 */
interface RequestHandlerFactory
{
	/**
	 * Creates a new <kbd>RequestHandler</kbd> appropriate for the request type.
	 *
	 * @param RequestType	$requestType
	 * @return RequestHandler	A <kbd>RequestHandler</kbd> object, if the factory is capable for producing appropriate
	 *							handlers; otherwise, NULL.
	 */
	public function newRequestHandler(RequestType $requestType);
}
