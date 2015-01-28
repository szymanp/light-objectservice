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


}