<?php
namespace Light\ObjectService\Service\Protocol;

class ExceptionResponseHelper
{
	/** @var \Exception */
	protected $exception;

	public function __construct(\Exception $e)
	{
		$this->exception = $e;
	}

}