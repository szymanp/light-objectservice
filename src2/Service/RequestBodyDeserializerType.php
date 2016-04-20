<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Type\CollectionType;
use Light\ObjectAccess\Type\ComplexType;
use Light\ObjectAccess\Type\SimpleType;
use Light\ObjectAccess\Type\Type;
use MabeEnum\Enum;

/**
 * Enumeration of possible deserializer types.
 */
final class RequestBodyDeserializerType extends Enum
{
	const SIMPLE_VALUE_REPRESENTATION 		= 1;
	const COMPLEX_VALUE_REPRESENTATION		= 2;
	const COLLECTION_VALUE_REPRESENTATION	= 3;
	const COMPLEX_VALUE_MODIFICATION		= 4;
	const COLLECTION_VALUE_MODIFICATION		= 5;
	const COLLECTION_ELEMENT_SELECTION		= 6;
	const COMPLEX_VALUE_ACTION				= 7;
	const COLLECTION_VALUE_ACTION			= 8;

	/**
	 * @param RequestBodyType $requestBodyType
	 * @param Type            $type
	 * @return RequestBodyDeserializerType
	 */
	public static function fromBodyAndResourceType(RequestBodyType $requestBodyType, Type $type)
	{
		switch($requestBodyType->getValue())
		{
			case RequestBodyType::ACTION:
				if ($type instanceof ComplexType)
				{
					return self::get(self::COMPLEX_VALUE_ACTION);
				}
				elseif ($type instanceof CollectionType)
				{
					return self::get(self::COLLECTION_VALUE_ACTION);
				}
				break;
			case RequestBodyType::MODIFICATION:
				if ($type instanceof ComplexType)
				{
					return self::get(self::COMPLEX_VALUE_MODIFICATION);
				}
				elseif ($type instanceof CollectionType)
				{
					return self::get(self::COLLECTION_VALUE_MODIFICATION);
				}
				break;
			case RequestBodyType::REPRESENTATION:
				if ($type instanceof SimpleType)
				{
					return self::get(self::SIMPLE_VALUE_REPRESENTATION);
				}
				elseif ($type instanceof ComplexType)
				{
					return self::get(self::COMPLEX_VALUE_REPRESENTATION);
				}
				elseif ($type instanceof CollectionType)
				{
					return self::get(self::COLLECTION_VALUE_REPRESENTATION);
				}
				break;
			case RequestBodyType::SELECTION:
				if ($type instanceof CollectionType)
				{
					return self::get(self::COLLECTION_ELEMENT_SELECTION);
				}
				break;
		}
		throw new \LogicException("Invalid combination of request body type " . $requestBodyType->getName() . " and resource type " . get_class($type));
	}
}
