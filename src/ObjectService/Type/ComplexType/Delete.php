<?php 
namespace Light\ObjectService\Type\ComplexType;

/**
 * An interface for ComplexTypes that support deletion of object instances.
 *
 */
interface Delete
{
	/**
	 * Deletes a new instance of an object of this complex-type.
	 * @param object			$object		Object to be deleted.
	 * @param DeletionContext	$context
	 */
	public function deleteObject($object, DeletionContext $context);
}