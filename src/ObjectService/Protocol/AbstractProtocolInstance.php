<?php
namespace Light\ObjectService\Protocol;

use Light\Exception\Exception;
use Light\ObjectService\Exception\HttpExceptionInformation;
use Light\ObjectService\Formats\Uri\UriSelectionProxy;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Service\EndpointRegistry;
use Light\ObjectService\Service\Protocol\ProtocolInstance;
use Light\ObjectService\Service\Protocol\SerializationHelper;
use Symfony\Component\HttpFoundation;

abstract class AbstractProtocolInstance implements ProtocolInstance
{
	/** @var HttpFoundation\Request */
	protected $httpRequest;

	/**
	 * @return SerializationHelper
	 */
	abstract protected function getSerializationHelper();

	protected function __construct(HttpFoundation\Request $httpRequest)
	{
		$this->httpRequest = $httpRequest;
	}

	/**
	 * Prepares a HTTP Response from an exception.
	 * @param \Exception $result
	 * @return HttpFoundation\Response
	 */
	public function prepareExceptionResponse(\Exception $result)
	{
		$serializer = $this->getSerializationHelper()->getExceptionSerializer($this->httpRequest);
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
			$serializer = $this->getSerializationHelper()->getResourceSerializer($this->httpRequest);
		}
		else
		{
			$serializer = $this->getSerializationHelper()->getValueSerializer($this->httpRequest);
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

	/**
	 * Reads the resource address from the request.
	 * @param EndpointRegistry $endpointRegistry
	 * @return \Light\ObjectService\Resource\Addressing\EndpointRelativeAddress
	 * @throws NotFound
	 */
	protected function readResourceAddress(EndpointRegistry $endpointRegistry)
	{
		$uri = $this->httpRequest->getUri();

		// Remove the query section
		if (($qm = strpos($uri, '?')) > -1)
		{
			$uri = substr($uri, 0, $qm);
		}

		$address = $endpointRegistry->getResourceAddress($uri);
		if (is_null($address))
		{
			throw new NotFound($this->httpRequest->getUri(), "No endpoint matching this address was found");
		}
		return $address;
	}

	/**
	 * Reads the selection from the query part of the URI.
	 * @return UriSelectionProxy|null
	 */
	protected function readSelection()
	{
		if ($this->httpRequest->query->has("select"))
		{
			return new UriSelectionProxy($this->httpRequest->query->get("select"));
		}
		else
		{
			return null;
		}
	}
}