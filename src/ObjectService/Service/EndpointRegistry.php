<?php
namespace Light\ObjectService\Service;

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
}