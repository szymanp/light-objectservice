<?php
namespace Light\ObjectService\Service\Util;

use Light\ObjectAccess\Exception\ResourceException;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Service\Endpoint;
use Light\ObjectService\Service\ObjectProvider;

class DefaultObjectProvider implements ObjectProvider
{
	/** @var Endpoint */
	private $endpoint;

	/** @var array<string, ResolvedResource> */
	private $resources = array();

	public function publishValue($address, $value)
	{
		$typeRegistry = $this->endpoint->getTypeRegistry();

	}

	/**
	 * Publish a resource.
	 *
	 * The resource must be constructed with a resource address that belongs to this endpoint.
	 *
	 * @param ResolvedResource $resource
	 * @return $this
	 * @throws ResourceException
	 */
	public function publishResource(ResolvedResource $resource)
	{
		$address = $resource->getAddress();

		if ($address instanceof EndpointRelativeAddress)
		{
			if ($address->getEndpoint() !== $this->endpoint)
			{
				$this->resources[$address->getLocalAddressAsString()] = $resource;
			}
			else
			{
				throw new ResourceException("Resource does not belong to this endpoint");
			}
		}
		else
		{
			throw new ResourceException("Resource must have an address that is a subclass of " . EndpointRelativeAddress::class);
		}

		return $this;
	}

	/**
	 * Sets an Endpoint to be used by this ObjectProvider.
	 * @param Endpoint $endpoint
	 */
	public function setEndpoint(Endpoint $endpoint)
	{
		$this->endpoint = $endpoint;
	}

	/**
	 * Returns a resource published at the given address.
	 * @param string $address
	 * @return ResolvedResource    A ResolvedResource object, if a resource corresponding to the given address exists;
	 *                          otherwise, NULL.
	 */
	public function getResource($address)
	{
		// TODO: Implement getResource() method.
	}

	/**
	 * Returns a string used for separating elements in the resource address.
	 * @return string
	 */
	public function getAddressElementSeparator()
	{
		return "/";
	}
}