<?php 
namespace Light\ObjectService;

use Light\ObjectService\Type\Type;

/**
 * A registry of URIs for types and resources.
 * 
 * Classes implementing this interface should provide URIs for types and a base URI for resources.
 * Resources are tied to the URI of the webserver - an URI of a resource must be accessible and resolve to the representation of the object.
 * Types are not tied to the URI of the webserver - they might use an URI that is not accessible.
 *
 */
interface NameRegistry
{
	/**
	 * Returns the URI for the given type.
	 * @param Type $type
	 * @return string	An URI for the specified type.
	 */
	public function getTypeUri(Type $type);

}
