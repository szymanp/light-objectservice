<?php
namespace Szyman\ObjectService\Service;

/**
 * A result indicating that processing of the request ended in an exception.
 */
class ExceptionRequestResult implements RequestResult
{
	/** @var \Exception */
	private $exception;
	
	/**
	 * @param \Exception	$exception	The exception that caused the request to fail.
	 */
	public function __construct(\Exception $exception)
	{
		$this->exception = $exception;
	}

	/**
	 * Returns the exception that caused the request to fail.
	 * @return \Exception
	 */
	final public function getException()
	{
		return $this->exception;
	}
}
