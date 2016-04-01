<?php
namespace Szyman\ObjectService\Service;

interface RequestBodyDeserializer
{
	/**
	 * Deserializes a value from the HTTP request body.
	 * @param string $content	The content to be deserialized.
	 * @return ValueRepresentation
	 */
	public function deserialize($content);
}