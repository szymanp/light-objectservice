<?php
namespace Szyman\ObjectService\Configuration;

/**
 * An interface for classes that resolve addresses to resources.
 *
 * The ObjectProvider operates with addresses that are relative to a service endpoint.
 * The addresses should not include a host name and a path to the root of the web API,
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