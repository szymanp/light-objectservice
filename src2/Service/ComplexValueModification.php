<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedObject;
use Szyman\ObjectService\Resource\RepresentationTransferException;

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
	 * @throws RepresentationTransferException    Thrown if a problem updating the object is encountered.
	 */
	public function updateObject(ResolvedObject $target, ExecutionEnvironment $environment);
}