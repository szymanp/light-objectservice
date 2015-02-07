<?php
namespace Light\ObjectService\Exception;

interface HttpExceptionInformation
{
	/**
	 * Returns the HTTP status code related to this exception.
	 * @return integer
	 */
	public function getHttpStatusCode();
}