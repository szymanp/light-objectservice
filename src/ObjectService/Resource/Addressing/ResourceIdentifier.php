<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\EndpointRegistry;
use Light\ObjectService\Resource\Query\Scope;
use Light\ObjectService\Resource\ResolvedValue;

abstract class ResourceIdentifier
{
	/**
	 * Creates a new ResourceIdentifier.
	 * @param string	$url	The URL identifying the resource.
	 * @param Scope 	$scope
	 * @return ResourceIdentifier
	 */
	public static function createFromUrl($url, Scope $scope = null)
	{
		return new UrlResourceIdentifier($url, $scope);
	}

	public static function createFromResource(ResolvedValue $resource, $partialUrl)
	{
		// TODO
	}

	/**
	 * @param EndpointRegistry $registry
	 * @return ResourcePath
	 */
	abstract public function resolve(EndpointRegistry $registry);
}