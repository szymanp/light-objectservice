<?php
namespace Light\ObjectService\Exception;

use Szyman\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * The request could not be understood by the server due to malformed syntax.
 */
class MalformedRequest extends Exception implements HttpExceptionInformation
{
	public function getHttpStatusCode()
	{
		return Response::HTTP_BAD_REQUEST;
	}
}