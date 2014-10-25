<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Resource\Query\Scope;

class ResourceIdentifier
{
	/** @var string */
	private $url;
	/** @var string */
	private $serviceEndpoint;
	/** @var ResourcePath */
	private $resourcePath;
	/** @var boolean */
	private $openCollection;
	/** @var Scope */
	private $scope;

	/**
	 * Creates a new ResourceIdentifier.
	 * @param $url
	 * @param Scope $scope
	 * @return ResourceIdentifier
	 */
	public static function create($url, Scope $scope = null)
	{
		$resourceIdentifier = new self;

		// TODO

		return $resourceIdentifier;
	}

	/**
	 * @param ObjectRegistry $registry
	 *
	 */
	public function resolve(ObjectRegistry $registry)
	{
		// TODO
	}
}