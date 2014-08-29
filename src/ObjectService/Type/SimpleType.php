<?php 

namespace Light\ObjectService\Type;

/**
 * A base class for simple types.
 *
 * A simple type describes objects for which we do not allow access to individual parts.
 * 
 */
abstract class SimpleType extends Type
{
	/**
	 * Returns the PHP type or class name of the objects supported by this complex type.
	 * @return string
	 */
	abstract public function getPhpType();
}