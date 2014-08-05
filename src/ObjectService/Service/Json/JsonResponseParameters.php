<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Service\Response\ResponseParameters;

class JsonResponseParameters implements ResponseParameters
{
	/**
	 * Send detailed information about exceptions.
	 * If set to true, the object sent when an exception occurs will contain details
	 * such as the stack trace.
	 * @var boolean
	 */
	public $detailedExceptions = true;
}