<?php
namespace Szyman\ObjectService\Service;

use MabeEnum\Enum;

/**
 * Enumeration of possible types for the request-body.
 */
final class RequestBodyType extends Enum
{
    /** The request has no body */
    const NONE              = 0;
    /** The body specifies a full representation of the object */
    const REPRESENTATION    = 1;
    /** The body specifies changes that should be applied to an existing object */
    const MODIFICATION      = 2;
    /** The body specifies a rule for selecting elements from a collection */
    const SELECTION         = 3;
    /** The body specifies an action to be executed on a resource */
    const ACTION            = 4;
}
