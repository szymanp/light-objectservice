<?php
namespace Light\ObjectService\Service;
use Light\ObjectAccess\Exception\AddressResolutionException;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;

/**
 * A registry storing service endpoints.
 */
final class EndpointRegistry
{
	/** @var Endpoint[]  */
	private $endpoints = array();

	/**
	 * Adds an Endpoint to this registry.
	 * @param Endpoint $endpoint
	 */
	public function addEndpoint(Endpoint $endpoint)
	{
		$this->endpoints[] = $endpoint;
	}

	/**
	 * Finds an Endpoint corresponding to the given URL.
	 * @param string $url
	 * @return Endpoint	An Endpoint object, if found; otherwise, NULL.
	 */
	public function findEndpoint($url)
	{
		foreach($this->endpoints as $endpoint)
		{
			$endpointUrl = $endpoint->getUrl();
			if (substr($url, 0, strlen($endpointUrl)) === $endpointUrl)
			{
				return $endpoint;
			}
		}
		return null;
	}

	/**
	 * Returns a resource address object corresponding to the given URL.
	 * @param string $url
	 * @return EndpointRelativeAddress	A resource address object, if the URL corresponds to a known endpoint;
	 *                                  otherwise, NULL.
	 */
	public function getResourceAddress($url)
	{
		$endpoint = $this->findEndpoint($url);
		if (!is_null($endpoint))
		{
			return EndpointRelativeAddress::create($endpoint, substr($url, strlen($endpoint->getUrl())));
		}
		else
		{
			return null;
		}
	}

	/**
	 * Returns a resource identified by the given URL.
	 * @param string	$url
	 * @return \Light\ObjectAccess\Resource\ResolvedResource
	 * @throws AddressResolutionException	If the resource cannot be found.
	 */
	public function getResource($url)
	{
		$address = $this->getResourceAddress($url);
		if (is_null($address))
		{
			throw new AddressResolutionException("No endpoint matches address <%1>", $url);
		}

		$relativeAddress = $address->getEndpoint()->findResource($address->getPathElements());

		if (is_null($relativeAddress))
		{
			throw new AddressResolutionException("Address <%1> could not be resolved to any resource", $url);
		}

		$relativeAddressReader = new RelativeAddressReader($relativeAddress);
		return $relativeAddressReader->read();
	}
}