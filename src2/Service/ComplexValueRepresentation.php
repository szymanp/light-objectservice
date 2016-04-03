<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedObject;

/**
 * A class implementing this interface replaces an existing complex value with a new one.
 */
interface ComplexValueRepresentation extends DeserializedBody
{
	// TODO: What exceptions does the method throw?

	/**
	 * Set the value of a complex resource to the one contained in this object.
	 *
	 * @param ResolvedObject       $target		The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 */
	public function replaceObject(ResolvedObject $target, ExecutionEnvironment $environment);
}