<?php
namespace Light\ObjectService\Service\Util;

use Light\ObjectAccess\Exception\ResourceException;
use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Service\Endpoint;
use Light\ObjectService\Service\ObjectProvider;

/**
 * An object provider where resources need to be published at predefined addresses.
 */
class DefaultObjectProvider implements ObjectProvider
{
	/** @var Endpoint */
	private $endpoint;
	/** @var TypeRegistry */
	private $typeRegistry;
	/** @var array<string, ResolvedResource> */
	private $resources = array();

	/**
	 * Constructs a new DefaultObjectProvider.
	 * @param TypeRegistry $typeRegistry
	 */
	public function __construct(TypeRegistry $typeRegistry)
	{
		$this->typeRegistry = $typeRegistry;
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
	 * Publishes the given value at the specified local address.
	 * @param string	$address	The address - relative to the endpoint.
	 * @param mixed		$value		The value to be published.
	 * @return $this
	 */
	public function publishValue($address, $value)
	{
		$typeHelper = $this->typeRegistry->getTypeHelperByValue($value);
		$addressObject = EndpointRelativeAddress::create($this->endpoint, $address);
		$this->resources[$address] = ResolvedValue::create($typeHelper, $value, $addressObject, Origin::unavailable());

		return $this;
	}

	/**
	 * Publishes a resource.
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
	 * Returns the TypeRegistry used by this ObjectProvider.
	 * @return TypeRegistry
	 */
	public function getTypeRegistry()
	{
		return $this->typeRegistry;
	}


	/**
	 * Returns a resource published at the given address.
	 * @param string $address
	 * @return ResolvedResource    A ResolvedResource object, if a resource corresponding to the given address exists;
	 *                          otherwise, NULL.
	 */
	public function getResource($address)
	{
		return @ $this->resources[$address];
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