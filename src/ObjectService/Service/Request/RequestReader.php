<?php
namespace Light\ObjectService\Service\Request;

use Light\Util\HTTP\Request as HTTPRequest;

/**
 * An interface for classes that can read a request.
 *
 */
interface RequestReader
{
	/**
	 * Returns true if the HTTP Request can be processed by this Request Reader.
	 * @param HTTPRequest $httpRequest
	 * @return boolean
	 */
	public function isAcceptable(HTTPRequest $httpRequest);
	
	/**
	 * Returns a list of content-types that are acceptable by this Request Reader.
	 * @return string[]
	 */
	public function getAcceptableContentTypes();
	
	/**
	 * Parse a HTTP Request and return a Service Request object.
	 * @param HTTPRequest $httpRequest
	 * @throws \Light\ObjectService\Exceptions\InvalidRequestException
	 * @return \Light\ObjectService\Service\Request\Request
	 */
	public function read(HTTPRequest $httpRequest);
}