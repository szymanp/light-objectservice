<?php
namespace Light\ObjectService\Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * The requested method was not allowed for the given resource.
 */
class MethodNotAllowed extends \Exception implements HttpExceptionInformation
{
	public function getHttpStatusCode()
	{
		return Response::HTTP_METHOD_NOT_ALLOWED;
	}
}