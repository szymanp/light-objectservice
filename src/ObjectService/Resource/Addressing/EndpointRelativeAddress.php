<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Resource\Addressing\ResourceAddress;
use Light\ObjectService\Service\Endpoint;

/**
 * A resource address that is relative to a service endpoint.
 */
class EndpointRelativeAddress implements ResourceAddress
{
	/** @var Endpoint */
	private $endpoint;

	/**
	 * Constructs a new address.
	 * @param Endpoint $endpoint
	 */
	public function __construct(Endpoint $endpoint)
	{
		$this->endpoint = $endpoint;
	}

	/**
	 * @param Scope $scope
	 * @return ResourceAddress  A new ResourceAddress object representing the original address
	 *                          with the scope object appended at the end.
	 */
	public function appendScope(Scope $scope)
	{
		// TODO: Implement appendScope() method.
	}

	/**
	 * @param string $pathElement
	 * @return ResourceAddress    A new ResourceAddress object representing the original address
	 *                            with the new element appended at the end.
	 */
	public function appendElement($pathElement)
	{
		// TODO: Implement appendElement() method.
	}

	public function hasStringForm()
	{
		// TODO: Implement hasStringForm() method.
	}

	public function getAsString()
	{
		// TODO: Implement getAsString() method.
	}

	/**
	 * Returns the local part of the address as a string.
	 *
	 * @return string	A string representing the address without the endpoint prefix, if a string form is available;
	 *                	otherwise, NULL.
	 */
	public function getLocalAddressAsString()
	{
		// TODO
	}

	/**
	 * Returns the endpoint that this address is relative to.
	 * @return Endpoint
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}
}