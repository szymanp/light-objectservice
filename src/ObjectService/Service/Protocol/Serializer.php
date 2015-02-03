<?php
namespace Light\ObjectService\Service\Protocol;

interface Serializer
{
	/**
	 * Returns the content type produced by this serializer.
	 * @return string
	 */
	public function getContentType();
}