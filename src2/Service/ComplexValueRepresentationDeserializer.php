<?php
namespace Szyman\ObjectService\Service;

interface ComplexValueRepresentationDeserializer extends RequestBodyDeserializer
{
	/**
	 * Deserializes a complex value.
	 * @param string $content	The content to be deserialized.
	 * @return ComplexValueRepresentation
	 */
	public function deserialize($content);
}