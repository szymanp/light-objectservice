<?php
namespace Light\ObjectService\Service\Protocol;

use Symfony\Component\HttpFoundation\Request;

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
	 * @param Request $httpRequest
	 * @return DeserializedResult
	 */
	public function deserialize(Request $httpRequest);
}