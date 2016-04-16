<?php
namespace Szyman\ObjectService\Request;

use Light\ObjectAccess\Type\ComplexTypeHelper;
use Light\ObjectAccess\Type\TypeHelper;
use Szyman\ObjectService\Request\Json\StandardJsonComplexValueDeserializer;
use Szyman\ObjectService\Service\RequestBodyDeserializer;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestBodyDeserializerType;

/**
 * A deserializer factory that instantiates standard deserializers available in the ObjectService package.
 */
final class StandardRequestBodyDeserializerFactory implements RequestBodyDeserializerFactory
{
	/**
	 * @var StandardRequestBodyDeserializerFactory
	 */
	private static $INSTANCE = null;

	/**
	 * Returns an instance of this factory.
	 * @return StandardRequestBodyDeserializerFactory
	 */
	public static function getInstance()
	{
		if (is_null(self::$INSTANCE)) self::$INSTANCE = new self;
		return self::$INSTANCE;
	}

	private function __construct()
	{
		// Private constructor - use the getInstance() static method.
	}

	/**
	 * Creates a new deserializer for the specified body type and resource type.
	 * @param RequestBodyDeserializerType $deserializerType The type of deserializer requested.
	 * @param string                      $contentType      The MIME content-type of the request-body.
	 * @param TypeHelper                  $typeHelper       The helper for the concrete type of the resource being deserialized.
	 * @return RequestBodyDeserializer    A deserializer, if this factory supports creating deserializers matching
	 *                                                      the specified parameters; otherwise, NULL.
	 */
	public function newRequestBodyDeserializer(RequestBodyDeserializerType $deserializerType, $contentType, TypeHelper $typeHelper)
	{
		if ($this->isJson($contentType))
		{
			switch($deserializerType->getValue())
			{
				case RequestBodyDeserializerType::SIMPLE_VALUE_REPRESENTATION:
					return null;	// TODO
				case RequestBodyDeserializerType::COMPLEX_VALUE_REPRESENTATION:
				case RequestBodyDeserializerType::COLLECTION_VALUE_MODIFICATION:
					if ($typeHelper instanceof ComplexTypeHelper)
					{
						return new StandardJsonComplexValueDeserializer($typeHelper);
					}
					else
					{
						throw new \LogicException();
					}
				case RequestBodyDeserializerType::COLLECTION_VALUE_REPRESENTATION:
				case RequestBodyDeserializerType::COLLECTION_VALUE_MODIFICATION:
					return null;	// TODO
				case RequestBodyDeserializerType::COLLECTION_ELEMENT_SELECTION:
					return null;	// TODO
				case RequestBodyDeserializerType::COMPLEX_VALUE_ACTION:
				case RequestBodyDeserializerType::COLLECTION_VALUE_ACTION:
					return null;	// TODO
			}
		}

		return null;
	}

	private function isJson($contentType)
	{
		return $contentType == "application/json";
	}
}