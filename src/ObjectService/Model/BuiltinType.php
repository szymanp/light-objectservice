<?php 

namespace Light\ObjectService\Model;

final class BuiltinType extends SimpleType
{
	private static $builtinTypes = array("bool", "boolean", "int", "integer", "float", "real", "string");
	
	private $type;
	
	/**
	 * Returns true if the specified type is one of the built-in PHP types.
	 * @param string $name
	 * @return boolean
	 */
	public static function isBuiltinType($name)
	{
		$name = strtolower(trim($name));
		return in_array($name, self::$builtinTypes);
	}

	public function __construct($type)
	{
		$this->type = $type;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Model\SimpleType::getPhpType()
	 */
	public function getPhpType()
	{
		return $this->type;
	}
	
}