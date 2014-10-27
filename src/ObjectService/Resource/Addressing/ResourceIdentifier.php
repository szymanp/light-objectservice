<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\EndpointRegistry;
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
		$resourceIdentifier = new self($url);
		
		$resourceIdentifier->scope = $scope;

		return $resourceIdentifier;
	}

	protected function __construct($url)
	{
		// protected constructor
		$this->url = $url;
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
		return new ResolvedResourceIdentifier($registry, $this);
	}
}