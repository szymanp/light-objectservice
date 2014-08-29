<?php 
namespace Light\ObjectService\Type\CollectionType;

/**
 * An interface for CollectionTypes that support removing elements from collection.
 *
 */
interface Remove
{
	/**
	 * Removes a given value from the collection.
	 * @param mixed	$collection
	 * @param mixed	$value
	 */
	public function removeValue($collection, $value);
}
