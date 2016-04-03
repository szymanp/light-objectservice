<?php
namespace Szyman\ObjectService\Service;

interface RequestBodyDeserializer
{
	/**
	 * Deserializes a value from the HTTP request body.
	 * @param string|resource $content	The content to be deserialized. This can be either a string or a stream resource.
	 * @return DeserializedBody
	 */
	public function deserialize($content);
}