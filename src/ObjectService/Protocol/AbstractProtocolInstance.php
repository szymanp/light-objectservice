<?php
namespace Light\ObjectService\Protocol;

use Light\Exception\Exception;
use Light\ObjectAccess\Query\Query;
use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Transaction\Transaction;
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
	/** @var Transaction */
	protected $transaction;

	/**
	 * @return SerializationHelper
	 */
	abstract protected function getSerializationHelper();

	protected function __construct(HttpFoundation\Request $httpRequest, Transaction $transaction)
	{
		$this->httpRequest = $httpRequest;
		$this->transaction = $transaction;
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

		// For POST methods, append a Location header indicating the first resource that was created.
		if ($this->httpRequest->getMethod() == "POST" && !empty($this->transaction->getCreatedResources()))
		{
			$firstNewResource = $this->transaction->getCreatedResources()[0];
			if ($firstNewResource->getAddress()->hasStringForm())
			{
				$response->headers->set("Location", $firstNewResource->getAddress()->getAsString());
			}
		}

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

		if ($this->httpRequest->query->has("count") || $this->httpRequest->query->has("offset"))
		{
			$count  = $this->httpRequest->query->getInt("count", null);
			$offset = $this->httpRequest->query->getInt("offset", null);
			$limitScope = Scope::createWithQuery(Query::emptyQuery(), $count, $offset);
			$address = $address->appendScope($limitScope);
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