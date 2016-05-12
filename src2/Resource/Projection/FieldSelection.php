<?php
namespace Szyman\ObjectService\Resource\Projection;

use Light\ObjectAccess\Type\ComplexType;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectService\Resource\Selection\Selection;

/**
 * An interface for complex types that can provide a custom field selection.
 */
interface FieldSelection extends ComplexType
{
    /**
     * Returns a selection of fields appropriate for the given object.
     * @param ResolvedObject $objet
     * @return Selection
     */
    public function getDefaultSelection(ResolvedObject $object);
}
