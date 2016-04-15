<?php
namespace Light\ObjectService\Exception;

use Szyman\Exception\MessageBuilder;

/**
 * An exception thrown if the request cannot be processed due to missing or incorrect configuration of some component.
 */
class ConfigurationException extends \RuntimeException
{
	use MessageBuilder;

	public function __construct($message)
	{
		$result = $this->prepareMessage(func_get_args());
		parent::__construct($result->message, $result->errorCode, $result->previousException);
	}
}