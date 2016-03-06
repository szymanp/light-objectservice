<?php
namespace Szyman\ObjectService\Configuration;

interface BodyDeserializerFactory
{
	// What about the return value?
	//

	public function newSimpleValueRepresentationDeserializer($contentType, SimpleType $type);
	
	public function newComplexValueRepresentationDeserializer($contentType, ComplexType $type);
	
	public function newCollectionValueRepresentationDeserializer($contentType, CollectionType $type);
	
	public function newComplexValueModificationDeserializer($contentType, ComplexType $type);
	
	public function newCollectionValueModificationDeserializer($contentType, CollectionType $type);
	
	public function newCollectionElementSelectionDeserializer($contentType, CollectionType $type);
	
	public function newComplexValueActionDeserializer($contentType, ComplexType $type);
	
	public function newCollectionxValueActionDeserializer($contentType, CollectionType $type);
}
