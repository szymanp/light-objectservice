<?php
namespace Light\ObjectService\Service\Protocol;

/**
 * A Serializer is a class that can convert a PHP value into a specific type of content.
 */
interface Serializer
{
	/**
	 * Returns the content type produced by this serializer.
	 * @return string
	 */
	public function getContentType();
}