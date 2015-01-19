<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedResource;

/**
 * An interface for classes that resolve addresses to resources.
 */
interface ObjectProvider
{
	/**
	 * Sets an Endpoint to be used by this ObjectProvider.
	 * @param Endpoint $endpoint
	 */
	public function setEndpoint(Endpoint $endpoint);

	/**
	 * Returns a resource published at the given address.
	 * @param string $address
	 * @return ResolvedResource	A ResolvedResource object, if a resource corresponding to the given address exists;
	 *                          otherwise, NULL.
	 */
	public function getResource($address);

	/**
	 * Returns a string used for separating elements in the resource address.
	 * @return string
	 */
	public function getAddressElementSeparator();
}