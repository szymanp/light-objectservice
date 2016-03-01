<?php
namespace Szyman\ObjectService\Configuration;

/**
 * An interface for classes that resolve addresses to resources.
 *
 * An ObjectProvider maps addresses to resources (or rather, resource factories).
 * The addresses used by this interface are relative to a service endpoint.
 * This means that they should _not_ include a host name and a path to the root of the web API,
 * as that information is captured by the Endpoint class.
 */
interface ObjectProvider
{
	/**
	 * Returns a factory for the resource corresponding to the given address.
	 * @param string $address
	 * @return EndpointResourceFactory A factory for creating instances of this resource, if the address matches;
	 *                                 otherwise, NULL.
	 */
	public function getResourceFactory($address);
}