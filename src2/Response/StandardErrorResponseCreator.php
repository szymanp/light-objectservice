<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Exception\HttpExceptionInformation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\Exception\InvalidArgumentException;
use Szyman\ObjectService\Service\ExceptionRequestResult;
use Szyman\ObjectService\Service\RequestComponents;
use Szyman\ObjectService\Service\RequestResult;
use Szyman\ObjectService\Service\ResponseCreator;

class StandardErrorResponseCreator implements ResponseCreator
{
	/** @var StructureSerializer */
	private $structureSerializer;
	/** @var DataSerializer */
	private $dataSerializer;

	/**
	 * Creates a new Response object.
	 * @param Request           $request
	 * @param RequestResult     $requestResult
	 * @param RequestComponents $requestComponents
	 * @return Response
	 * @throws \InvalidArgumentException	Thrown if <kbd>$requestResult</kbd> is not of a supported type.
	 */
	final public function newResponse(Request $request, RequestResult $requestResult, RequestComponents $requestComponents = null)
	{
		// Check if the request result is applicable.
		if (!($requestResult instanceof ExceptionRequestResult))
		{
			throw InvalidArgumentException::newInvalidType('$requestResult', $requestResult, ExceptionRequestResult::class);
		}

		// Prepare the response object.
		$status = $this->getStatusCode($requestResult->getException());
		return $this->createResponse($content, $status, $headers);
	}

	/**
	 * Return a new Response instance.
	 * @param mixed $content
	 * @param int   $status
	 * @param array $headers
	 * @return Response
	 */
	protected function createResponse($content, $status, array $headers)
	{
		return new Response($content, $status, $headers);
	}

	/**
	 * Returns the status code for this exception object.
	 * @param \Exception $e
	 * @return int
	 */
	protected function getStatusCode(\Exception $e)
	{
		if ($e instanceof HttpExceptionInformation)
		{
			return $e->getHttpStatusCode();
		}
		else
		{
			return Response::HTTP_INTERNAL_SERVER_ERROR;
		}
	}
}