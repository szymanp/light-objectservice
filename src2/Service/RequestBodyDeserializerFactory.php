<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Type\ComplexType;
use Light\ObjectAccess\Type\SimpleType;
use Light\ObjectService\Exception\UnsupportedMediaType;

/**
 * A factory for objects that can deserialize a specific type of body of a request based on a content-type.
 */
interface RequestBodyDeserializerFactory
{
	/**
	 * Creates a new deserializer for a full representation of a simple value.
	 * @param string     $contentType
	 * @param SimpleType $simpleType
	 * @throws UnsupportedMediaType	If the factory cannot handle the specified content-type.
	 * @return SimpleValueRepresentationDeserializer
	 */
	public function newSimpleValueRepresentationDeserializer($contentType, SimpleType $simpleType);

	/**
	 * Creates a new deserializer for a full representation of an object.
	 * @param string      $contentType
	 * @param ComplexType $complexType
	 * @throws UnsupportedMediaType	If the factory cannot handle the specified content-type.
	 * @return ComplexValueRepresentationDeserializer
	 */
	public function newComplexValueRepresentationDeserializer($contentType, ComplexType $complexType);

	/**
	 * Creates a new deserializer for a partial representation of an object.
	 * @param string      $contentType
	 * @param ComplexType $complexType
	 * @throws UnsupportedMediaType	If the factory cannot handle the specified content-type.
	 * @return ComplexValueModificationDeserializer
	 */
	public function newComplexValueModificationDeserializer($contentType, ComplexType $complexType);
}