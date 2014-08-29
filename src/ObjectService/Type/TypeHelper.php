<?php 

namespace Light\ObjectService\Type;

use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Type\Builder\BaseFieldBuilder;
use Light\Exception\Exception;

/**
 * A helper class for working with types and fields.
 *
 */
final class TypeHelper
{
	/** @var \Light\ObjectService\ObjectRegistry */
	private $registry;
	
	/**
	 * Creates a new TypeHelper instance.
	 * @param ObjectRegistry $registry
	 * @return \Light\ObjectService\Type\TypeHelper
	 */
	public static function create(ObjectRegistry $registry)
	{
		return new self($registry);
	}
	
	public function __construct(ObjectRegistry $registry)
	{
		$this->registry = $registry;
	}
	
	/**
	 * Returns a type object for the given type.
	 * @param string 	$typeName
	 * @param boolean 	$isCollection
	 * @throws TypeHelper_Exception	If the type is not registered.
	 * @return \Light\ObjectService\Type\Type
	 */
	public function getType($typeName, $isCollection)
	{
		$type = $isCollection ? $this->registry->getCollectionType($typeName)
							  : $this->registry->getType($typeName);
		if (is_null($type))
		{
			throw new TypeHelper_Exception("PHP type \"%1\" is not registered with the object registry", $clazz);
		}
		return $type;
	}
	
	/**
	 * Returns a type object for the given field.
	 * @param BaseFieldBuilder $field
	 * @throws TypeHelper_Exception	If the field does not specify a type.
	 * @return \Light\ObjectService\Type\Type
	 */
	public function getTypeForField(BaseFieldBuilder $field)
	{
		$typeName = $field->getTypeName();
		if (empty($typeName))
		{
			throw new TypeHelper_Exception("No type specified for field \"%1\"", $field->getPropertyName());
		}
		
		return $this->getType($typeName, $field->isCollection());
	}
	
	/**
	 * Returns the type of the given field if the type is an ObjectProvider, otherwise NULL.
	 * @param BaseFieldBuilder $field
	 * @return \Light\ObjectService\Type\ObjectProvider
	 */
	public function getObjectProviderOrNull(BaseFieldBuilder $field)
	{
		$type = $this->getTypeForField($field);
		if ($type instanceof ObjectProvider)
		{
			return $type;
		}
		else
		{
			return null;
		}
	}
}

class TypeHelper_Exception extends Exception
{
}