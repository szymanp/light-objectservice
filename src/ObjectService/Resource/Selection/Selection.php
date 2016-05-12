<?php
namespace Light\ObjectService\Resource\Selection;

use Light\ObjectAccess\Type\ComplexTypeHelper;

/**
 * A selection specifies for which fields should values returned in the service response.
 */
abstract class Selection
{
	/**
	 * Creates a new Selection object.
	 * @param ComplexTypeHelper $typeHelper
	 * @return RootSelection
	 */
	public static function create(ComplexTypeHelper $typeHelper)
	{
		return new RootSelection($typeHelper);
	}
    
    public static function proxy()
    {
        
    }

	/**
	 * Returns a list of field names to be selected.
	 * @return string[]
	 */
	abstract public function getFields();

	/**
	 * Returns a selection for a dependent object accessible via a named field.
	 * @param string $fieldName
	 * @return NestedSelection	A selection object, if defined for the field; otherwise, NULL.
	 */
	abstract public function getSubSelection($fieldName);

	/**
	 * Returns the type helper for the type that this selection is applicable to.
	 * @return ComplexTypeHelper
	 */
	abstract public function getTypeHelper();
}