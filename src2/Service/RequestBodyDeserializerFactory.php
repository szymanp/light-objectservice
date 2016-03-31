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
	// TODO We shouldn't throw an exception, just return NULL.
	//      For example, Java's HashMap::get() returns NULL if the map doesn't contain the key.

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

	/* TODO
	public function newCollectionValueRepresentationDeserializer($contentType, CollectionType $type);

	public function newCollectionValueModificationDeserializer($contentType, CollectionType $type);

	public function newCollectionElementSelectionDeserializer($contentType, CollectionType $type);

	public function newComplexValueActionDeserializer($contentType, ComplexType $type);

	public function newCollectionxValueActionDeserializer($contentType, CollectionType $type);
	 */
}