<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedObject;
use Szyman\ObjectService\Resource\RepresentationTransferException;

/**
 * A class implementing this interface replaces an existing complex value with a new one.
 */
interface ComplexValueRepresentation extends DeserializedBody
{
	/**
	 * Set the value of a complex resource to the one contained in this object.
	 *
	 * @param ResolvedObject       $target		The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 * @throws RepresentationTransferException    Thrown if a problem replacing the object is encountered.
	 */
	public function replaceObject(ResolvedObject $target, ExecutionEnvironment $environment);
}