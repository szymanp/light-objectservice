<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Type\TypeHelper;

/**
 * A factory for objects that can deserialize a specific type of body of a request based on a content-type.
 */
interface RequestBodyDeserializerFactory
{
	/**
	 * Creates a new deserializer for the specified body type and resource type.
	 * @param RequestBodyDeserializerType $deserializerType	The type of deserializer requested.
	 * @param string                      $contentType      The MIME content-type of the request-body.
	 * @param TypeHelper                  $typeHelper       The helper for the concrete type of the resource being deserialized.
	 * @return RequestBodyDeserializer    A deserializer, if this factory supports creating deserializers matching
	 *                                 	  the specified parameters; otherwise, NULL.
	 */
	public function newRequestBodyDeserializer(RequestBodyDeserializerType $deserializerType, $contentType, TypeHelper $typeHelper);
}
