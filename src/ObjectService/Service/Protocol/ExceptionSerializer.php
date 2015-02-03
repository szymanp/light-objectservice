<?php
namespace Light\ObjectService\Service\Protocol;

interface ExceptionSerializer extends Serializer
{
	/**
	 * Serializes an exception.
	 * @param \Exception $e
	 * @return mixed
	 */
	public function serialize(\Exception $e);

}