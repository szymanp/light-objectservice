<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\Service\Endpoint;

/**
 * A structure for storing data from a resolution of a ResourceIdentifier.
 */
class ResolvedResourceIdentifier
{
	/** @var ResourcePath */
	private $resourcePath;

	/** @var Endpoint */
	private $endpoint;

	/**
	 * @return Endpoint
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	/**
	 * @param Endpoint $endpoint
	 */
	public function setEndpoint($endpoint)
	{
		$this->endpoint = $endpoint;
	}

	/**
	 * @return ResourcePath
	 */
	public function getResourcePath()
	{
		return $this->resourcePath;
	}

	/**
	 * @param ResourcePath $resourcePath
	 */
	public function setResourcePath($resourcePath)
	{
		$this->resourcePath = $resourcePath;
	}

} 