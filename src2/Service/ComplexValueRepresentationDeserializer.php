<?php
namespace Szyman\ObjectService\Service;

interface ComplexValueRepresentationDeserializer
{
	/**
	 * Deserializes a complex value.
	 * @param string $content	The content to be deserialized.
	 * @return ComplexValueRepresentation
	 */
	public function deserialize($content);
}