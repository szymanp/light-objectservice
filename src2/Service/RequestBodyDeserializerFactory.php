<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Type\Type;

/**
 * A factory for objects that can deserialize a specific type of body of a request based on a content-type.
 */
interface RequestBodyDeserializerFactory
{
	/**
	 * Creates a new deserializer for the specified body type and resource type.
	 * @param RequestBodyDeserializerType $deserializerType
	 * @param string         			  $contentType
	 * @param Type           			  $type
	 * @return RequestBodyDeserializer    A deserializer, if this factory supports creating deserializers matching
	 *                                 	  the specified parameters; otherwise, NULL.
	 */
	public function newRequestBodyDeserializer(RequestBodyDeserializerType $deserializerType, $contentType, Type $type);
}
