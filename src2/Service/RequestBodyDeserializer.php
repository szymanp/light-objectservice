<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectService\Exception\MalformedRequest;

interface RequestBodyDeserializer
{
	/**
	 * Deserializes a value from the HTTP request body.
	 * @param string|resource $content	The content to be deserialized. This can be either a string or a stream resource.
	 * @return DeserializedBody
	 * @throws MalformedRequest	Thrown if the content does not match the expected format.
	 */
	public function deserialize($content);
}
