<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedCollection;
use Szyman\ObjectService\Resource\RepresentationTransferException;

/**
 * A class implementing this interface transforms an existing complex value.
 */
interface CollectionValueModification extends DeserializedBody
{
	// TODO: What exceptions does the method throw?

	/**
	 * Updates the value of a collection to the one contained in this object.
	 *
	 * @param ResolvedCollection   $target		The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 * @throws RepresentationTransferException    Thrown if a problem updating the object is encountered.
	 */
	public function updateCollection(ResolvedCollection $target, ExecutionEnvironment $environment);
}