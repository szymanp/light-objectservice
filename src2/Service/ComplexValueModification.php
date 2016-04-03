<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedObject;

/**
 * A class implementing this interface transforms an existing complex value.
 */
interface ComplexValueModification extends DeserializedBody
{
	// TODO: What exceptions does the method throw?

	/**
	 * Updates the value of a complex resource to the one contained in this object.
	 *
	 * @param ResolvedObject       $target		The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 */
	public function updateObject(ResolvedObject $target, ExecutionEnvironment $environment);
}