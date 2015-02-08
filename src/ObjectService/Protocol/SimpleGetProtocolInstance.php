<?php
namespace Light\ObjectService\Protocol;

use Light\Exception\Exception;
use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Exception\HttpExceptionInformation;
use Light\ObjectService\Exception\NotFound;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Service\Protocol\ProtocolInstance;
use Light\ObjectService\Service\Request;
use Light\ObjectService\Service\Util\SettableRequest;
use Symfony\Component\HttpFoundation;

class SimpleGetProtocolInstance implements ProtocolInstance
{
	/** @var SimpleGetProtocol */
	private $protocol;
	/** @var Transaction */
	private $transaction;
	/** @var HttpFoundation\Request */
	private $httpRequest;

	public function __construct(SimpleGetProtocol $protocol, HttpFoundation\Request $httpRequest, Transaction $transaction)
	{
		$this->protocol = $protocol;
		$this->httpRequest = $httpRequest;
		$this->transaction = $transaction;
	}

	/**
	 * Reads the request.
	 * @return Request
	 */
	public function readRequest()
	{
		$request = new SettableRequest();
		$address = $this->protocol->getEndpointRegistry()->getResourceAddress($this->httpRequest->getUri());
		if (is_null($address))
		{
			throw new NotFound($this->httpRequest->getUri(), "No endpoint matching this address was found");
		}
		$request->setResourceAddress($address);
		// TODO Selection
		return $request;
	}

	/**
	 * Prepares a HTTP Response from an exception.
	 * @param \Exception $result
	 * @return HttpFoundation\Response
	 */
	public function prepareExceptionResponse(\Exception $result)
	{
		$serializer = $this->protocol->getSerializationHelper()->getExceptionSerializer($this->httpRequest);
		if (is_null($serializer))
		{
			throw new Exception("No exception serializer found", 0, $result);
		}

		$statusCode = 500;

		if ($result instanceof HttpExceptionInformation)
		{
			$statusCode = $result->getHttpStatusCode();
		}

		$content = $serializer->serialize($result);
		$response = new HttpFoundation\Response($content, $statusCode);
		$response->headers->set("Content-Type", $serializer->getContentType());

		return $response;
	}

	/**
	 * Prepares a HTTP Response from a resource.
	 * @param mixed|DataEntity $result
	 * @return HttpFoundation\Response
	 */
	public function prepareResourceResponse($result)
	{
		if ($result instanceof DataEntity)
		{
			$serializer = $this->protocol->getSerializationHelper()->getResourceSerializer($this->httpRequest);
		}
		else
		{
			$serializer = $this->protocol->getSerializationHelper()->getValueSerializer($this->httpRequest);
		}

		if (is_null($serializer))
		{
			throw new Exception("No resource serializer found");
		}

		$content = $serializer->serialize($result);
		$response = new HttpFoundation\Response($content);
		$response->headers->set("Content-Type", $serializer->getContentType());

		return $response;
	}
}