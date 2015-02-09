<?php
namespace Light\ObjectService\Service\Protocol;

use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Service\Request;
use Symfony\Component\HttpFoundation;

/**
 * A ProtocolInstance is a class that can read a request and respond to it.
 */
interface ProtocolInstance
{
	/**
	 * Reads the request.
	 * @return Request
	 */
	public function readRequest();

	/**
	 * Prepares a HTTP Response from an exception.
	 * @param \Exception $result
	 * @return HttpFoundation\Response
	 */
	public function prepareExceptionResponse(\Exception $result);

	/**
	 * Prepares a HTTP Response from a resource.
	 * @param mixed|DataEntity $result
	 * @return HttpFoundation\Response
	 */
	public function prepareResourceResponse($result);
}