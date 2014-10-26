<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\ObjectRegistry;

/**
 * Provides information about a service endpoint.
 */
final class Endpoint
{
	/**
	 * The URL of this endpoint.
	 * @var string
	 */
	private $url;
	/** @var ObjectRegistry */
	private $objectRegistry;

	/**
	 * Creates a new Endpoint with the given URL.
	 * @param string	$url
	 * @return Endpoint
	 */
	public static function create($url)
	{
		return new self($url);
	}

	/**
	 * Creates an Endpoint for internal use (e.g. for testing.)
	 * @return Endpoint
	 */
	public static function createInternal()
	{
		return new self("//");
	}

	private function __construct($url)
	{
		if (substr($url, -1, 1) != "/")
		{
			$url .= "/";
		}

		$this->url			  = $url;
		$this->objectRegistry = new ObjectRegistry();
	}

	/**
	 * Returns the URL of this endpoint.
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Returns the ObjectRegistry for this service endpoint.
	 * @return ObjectRegistry
	 */
	public function getObjectRegistry()
	{
		return $this->objectRegistry;
	}
} 