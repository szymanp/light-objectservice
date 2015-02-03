<?php
namespace Light\ObjectService\Service\Protocol;

interface ValueSerializer extends Serializer
{
	/**
	 * Serializes a simple value.
	 * @param mixed $value
	 * @return mixed
	 */
	public function serialize($value);
}