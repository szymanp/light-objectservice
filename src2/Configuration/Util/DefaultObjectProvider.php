<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Type\CollectionType;
use Szyman\ObjectService\Configuration\EndpointResourceFactory;
use Szyman\ObjectService\Configuration\ObjectProvider;

/**
 * An object provider where resources need to be published at predefined addresses.
 */
class DefaultObjectProvider implements ObjectProvider
{
	/** @var array<string, EndpointResourceFactory> */
	private $resources = array();

	/**
	 * Publishes the given value at the specified local address.
	 * @param string	$address	The address - relative to the endpoint.
	 * @param mixed		$value		The value to be published.
	 * @return $this
	 */
	public function publishValue($address, $value)
	{
		$this->resources[$address] = EndpointResourceFactory::newValueFactory($address, $value);
		return $this;
	}

	/**
	 * Publishes a collection type.
	 *
	 * @param string         		$address
	 * @param string|CollectionType $collectionType A type name or a CollectionType instance.
	 * @param Origin         		$origin			If no origin is specified, an unavailable origin will be used.
	 * @return $this
	 */
	public function publishCollection($address, $collectionType, Origin $origin = null)
	{
		$this->resources[$address] = EndpointResourceFactory::newCollectionFactory($address, $collectionType, $origin);
		return $this;
	}

	/**
	 * @param string $address
	 * @return EndpointResourceFactory
	 */
	public function getResourceFactory($address)
	{
		return @ $this->resources[$address];
	}
}