<?php
namespace Szyman\ObjectService\Service;

use MabeEnum\Enum;

/**
 * Enumeration of possible types of requests.
 */
final class RequestType extends Enum
{
	/**
	 * A request for returning an existing resource.
	 *
	 * This request is simply a request for reading the resource identified by the request-uri.
	 */
	const READ		= 0;

	/**
	 * A request to create a new resource.
	 *
	 * A creation request can be:
	 * - a PUT request with a request-uri for a resource that does not exist yet; in this case, the resource type
	 *   is a ComplexType
	 * - a POST request where the request-uri resolves to a collection resource; in this case, the resource type
	 *   is a CollectionType, even though the created object can be of another type.
	 */
	const CREATE	= 1;

	/**
	 * A request to modify an existing resource.
	 *
	 * A modification request can be:
	 * - a PATCH request with a request-uri identifying a ComplexType resource,
	 * - a PATCH request with a request-uri identifying a CollectionType resource.
	 */
	const MODIFY	= 2;

	/**
	 * A request to replace an existing resource with a new one.
	 *
	 * A replacement request is a PUT request with a request-uri that identifies a SimpleType, ComplexType
	 * or CollectionType resource.
	 */
	const REPLACE	= 3;

	/**
	 * A request to delete an existing resource or resources.
	 *
	 * A deletion request is a DELETE request with a request-uri that identifies a ComplexType or a CollectionType
	 * resource. Note that in the latter case, the deletion pertains not to the collection itself, but to the certain
	 * elements in the collection.
	 */
	const DELETE	= 4;
	
	/**
	 * A request to execute a custom action on a resource.
	 *
	 * An action request is a POST request with a request-uri that identifies a ComplexType or a CollectionType
	 * resource.
	 */
	const ACTION	= 5;
}
