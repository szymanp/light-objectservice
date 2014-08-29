<?php
namespace Light\ObjectService\Type\ComplexType;

/**
 * Provides information about the context in which a complex-type object is created.
 */
interface CreationContext
{
	/**
	 * Returns the relatedobject.
	 * 
	 * An object to be created will be assigned to a property of another object.
	 * In this case, the object on which the property is defined is called the "related" object.
	 * 
	 * @return mixed	An object or collection, if there is one; otherwise, NULL.
	 */
	public function getContextObject();
}