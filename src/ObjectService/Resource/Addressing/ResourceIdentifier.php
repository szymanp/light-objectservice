<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\EndpointRegistry;
use Light\ObjectService\Exceptions\ResolutionException;
use Light\ObjectService\Resource\Query\Scope;

class ResourceIdentifier
{
	/** @var string */
	private $url;

	/** @var Scope */
	protected $scope;

	/**
	 * Creates a new ResourceIdentifier.
	 * @param string	$url	The URL identifying the resource.
	 * @param Scope 	$scope
	 * @return ResourceIdentifier
	 */
	public static function create($url, Scope $scope = null)
	{
		$resourceIdentifier = new self;

		$resourceIdentifier->url = $url;
		$resourceIdentifier->scope = $scope;

		return $resourceIdentifier;
	}

	protected function __construct()
	{
		// protected constructor
	}

	/**
	 * @return Scope
	 */
	public function getScope()
	{
		return $this->scope;
	}

	/**
	 * Returns the original URL for this resource.
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param ObjectRegistry $registry
	 *
	 */
	public function resolve(EndpointRegistry $registry)
	{
		$endpoint = $registry->findEndpoint($this->url);
		if (is_null($endpoint))
		{
			throw new ResolutionException("URL \"%1\" does not correspond to any known service endpoint", $this->url);
		}

		$url = substr($this->url, strlen($endpoint->getUrl()) + 1);
		print $url;
	}
}