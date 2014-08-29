<?php 
namespace Light\ObjectService\Type\ComplexType;

/**
 * An interface for ComplexTypes that support creating new instances of objects.
 *
 */
interface Create
{
	/**
	 * Creates a new instance of an object of this complex-type.
	 * @param CreationContext	$context
	 * @return object
	 */
	public function createObject(CreationContext $context);
}