<?php 
namespace Light\ObjectService\Model\CollectionType;

/**
 * An interface for CollectionTypes that support inserting elements into a collection at arbitrary positions.
 *
 */
interface Insert
{
	/**
	 * Inserts a value into the collection at a specified position.
	 * @param mixed	$collection
	 * @param mixed	$value
	 * @param mixed	$insertBefore	A key or value. 
	 */
	public function insertValue($collection, $value, $insertBefore);
}