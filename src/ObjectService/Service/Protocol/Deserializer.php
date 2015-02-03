<?php
namespace Light\ObjectService\Service\Protocol;

/**
 * A deserializer for request entities.
 */
interface Deserializer
{
	/**
	 * Returns a list of content types supported by this serializer.
	 * @return string[]
	 */
	public function getContentTypes();

	/**
	 * Deserializes the request entity.
	 * @param string $contentType
	 * @param string $requestEntity
	 * @return DeserializedResult
	 */
	public function deserialize($contentType, $requestEntity);
}