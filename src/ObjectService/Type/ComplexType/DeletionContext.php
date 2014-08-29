<?php
namespace Light\ObjectService\Type\ComplexType;

/**
 * Provides information about the context in which a complex-type object is deleted.
 */
interface DeletionContext
{
	/**
	 * Returns the related object.
	 * 
	 * An object to be deleted can be accessed via a property of another object.
	 * In this case, the object on which the property is defined is called the "related" object.
	 * 
	 * @return mixed	An object or collection, if there is one; otherwise, NULL.
	 */
	public function getContextObject();
}