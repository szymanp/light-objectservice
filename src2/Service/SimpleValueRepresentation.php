<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedScalar;

/**
 * A class implementing this interface replaces an existing simple value with a new one.
 */
interface SimpleValueRepresentation extends DeserializedBody
{
	// TODO: What exceptions does the method throw?

	/**
	 * Set the value of a simple resource to the one contained in this object.
	 *
	 * @param ResolvedScalar       $target		The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 */
	public function replaceValue(ResolvedScalar $target, ExecutionEnvironment $environment);
}