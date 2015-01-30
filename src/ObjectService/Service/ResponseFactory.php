<?php
namespace Light\ObjectService\Service;

use Symfony\Component\HttpFoundation;

interface ResponseFactory
{
	/**
	 * Returns a list of HTTP content types that this response can produce.
	 * @return string[]
	 */
	public function getContentTypes();

	/**
	 * Returns a new Response object.
	 * @return Response
	 */
	public function getResponse();

	/**
	 * Returns true if the HTTP Request can be processed by this Response Factory.
	 * @param HttpFoundation\Request $httpRequest
	 * @return boolean
	 */
	public function isAcceptable(HttpFoundation\Request $httpRequest);
}