<?php
namespace Light\ObjectService\Service;
use Light\ObjectAccess\Exception\AddressResolutionException;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Szyman\ObjectService\Configuration\Endpoint;

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
	 * @param string $actualEndpointUrl		This pass-by-reference parameter will be set to the endpoint
	 *										URL that matched.
	 * @return Endpoint	An Endpoint object, if found; otherwise, NULL.
	 */
	public function findEndpoint($url, & $actualEndpointUrl)
	{
		foreach($this->endpoints as $endpoint)
		{
			foreach($endpoint->getUrls() as $endpointUrl)
			{
				if (substr($url, 0, strlen($endpointUrl)) === $endpointUrl)
				{
					$actualEndpointUrl = $endpointUrl;
					return $endpoint;
				}
			}
		}
		return null;
	}

	/**
	 * Finds an endpoint corresponding to the given URL and translates that URL into an endpoint-relative address.
	 *
	 * Note that an EndpointRelativeAddress will always resolve to the primary URL of the endpoint,
	 * even if the supplied URL was an alternative URL. In other words, based on the returned address object
	 * it is impossible to determine the actual URL that was used as it could have been replaced by the primary URL.
	 *
	 * @param string $url
	 * @return EndpointRelativeAddress	A resource address object, if the URL corresponds to a known endpoint;
	 *                                  otherwise, NULL.
	 */
	public function getResourceAddress($url)
	{
		$actualEndpointUrl = null;
		
		$endpoint = $this->findEndpoint($url, $actualEndpointUrl);
		if (!is_null($endpoint))
		{
			return EndpointRelativeAddress::create($endpoint, substr($url, strlen($actualEndpointUrl)));
		}
		else
		{
			return null;
		}
	}

	/**
	 * Returns a  
	 * @param string $url
	 * @return RelativeAddress
	 * @throws AddressResolutionException	If the resource does not match an endpoint or a resource within the endpoint.
	 */
	public function getResourceRelativeAddress($url)
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

		return $relativeAddress;
	}

	/**
	 * Returns a resource identified by the given URL.
	 * @param string	$url
	 * @return \Light\ObjectAccess\Resource\ResolvedResource
	 * @throws AddressResolutionException	If the resource does not match any endpoint or published resource.
	 */
	public function getResource($url)
	{
		$relativeAddressReader = new RelativeAddressReader($this->getResourceRelativeAddress($url));
		return $relativeAddressReader->read();
	}
}