<?php
namespace Szyman\ObjectService\Service;

/**
 * An interface for classes that can deserialize simple values.
 */
interface SimpleValueRepresentationDeserializer extends RequestBodyDeserializer
{
	/**
	 * Deserializes a simple value.
	 * @param string $content	The content to be deserialized.
	 * @return SimpleValueRepresentation
	 */
	public function deserialize($content);
}