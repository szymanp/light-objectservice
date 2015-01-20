<?php
namespace Light\ObjectService\Resource\Selection;

use Light\ObjectAccess\Type\ComplexTypeHelper;

/**
 * A proxy selection that can be used if the {@link ComplexTypeHelper} object is not available at a given time.
 */
abstract class RootSelectionProxy extends Selection
{
	abstract public function createSelection(ComplexTypeHelper $typeHelper);

	final public function prepare(ComplexTypeHelper $typeHelper)
	{
		// TODO
	}

	/**
	 * Returns a list of field names to be selected.
	 * @return string[]
	 */
	final public function getFields()
	{
		// TODO: Implement getFields() method.
	}

	/**
	 * Returns a selection for a dependent object accessible via a named field.
	 * @param string $fieldName
	 * @return NestedSelection    A selection object, if defined for the field; otherwise, NULL.
	 */
	final public function getSubSelection($fieldName)
	{
		// TODO: Implement getSubSelection() method.
	}

	/**
	 * Returns the type helper for the type that this selection is applicable to.
	 * @return ComplexTypeHelper
	 */
	final public function getTypeHelper()
	{
		// TODO: Implement getTypeHelper() method.
	}

}