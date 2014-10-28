<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\Service\Endpoint;

/**
 * An URL that is relative to a service endpoint.
 */
final class EndpointUrl
{
	private $endpoint;
	private $relativeUrl;

	/**
	 * Creates a new EndpointUrl object.
	 * @param Endpoint $endpoint
	 * @param string   $relativeUrl
	 * @return EndpointUrl
	 */
	public static function create(Endpoint $endpoint, $relativeUrl)
	{
		return new self($endpoint, $relativeUrl);
	}

	private function __construct(Endpoint $endpoint, $relativeUrl)
	{
		$this->endpoint = $endpoint;
		$this->relativeUrl = $relativeUrl;
	}

	/**
	 * Returns the full URL string.
	 * @return string
	 */
	public function getUrl()
	{
		return $this->endpoint->getUrl() . $this->relativeUrl;
	}

	/**
	 * @return Endpoint
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	/**
	 * @return mixed
	 */
	public function getRelativeUrl()
	{
		return $this->relativeUrl;
	}


} 