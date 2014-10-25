<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Resource\Query\Scope;

final class ResourceIdentifier
{
	/** @var string */
	private $url;
	/** @var string */
	private $serviceEndpoint;
	/** @var string */
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

}