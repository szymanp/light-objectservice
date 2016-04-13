<?php
namespace Szyman\ObjectService\Resource;

use Szyman\Exception\MessageBuilder;

/**
 * An exception thrown if a transfer of a resource representation to the concrete resource fails.
 */
class RepresentationTransferException extends \RuntimeException
{
	use MessageBuilder;

	public function __construct($message)
	{
		$result = $this->prepareMessage(func_get_args());
		parent::__construct($result->message, $result->errorCode, $result->previousException);
	}
}
