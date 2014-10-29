<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\Exception\Exception;
use Light\ObjectService\Service\Endpoint;
use Light\Util\URL;

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

	/**
	 * Returns a new EndpointUrl that has the given relative URL path appended to this one.
	 * @param string $relativeUrl
	 * @throws Exception	If the paths cannot be joined.
	 * @return EndpointUrl
	 */
	public function join($relativeUrl)
	{
		if (strpos($this->relativeUrl, "?") !== false || strpos($this->relativeUrl, "#") !== false)
		{
			throw new Exception("The URL \"%1\" cannot be appended to as it contains a query or anchor part", $this->getUrl());
		}

		return new self($this->endpoint, URL::joinPaths(array($this->relativeUrl, $relativeUrl)));
	}
} 