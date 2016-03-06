<?php
namespace Light\ObjectService\Json\Request;
use Szyman\ObjectService\Service\ExecutionEnvironment;

/**
 * Base class for readers.
 */
abstract class Reader
{
	/** @var ExecutionEnvironment */
	protected $executionParameters;

	public function __construct(ExecutionEnvironment $executionParameters)
	{
		$this->executionParameters = $executionParameters;
	}

	/**
	 * @return ExecutionEnvironment
	 */
	public function getExecutionParameters()
	{
		return $this->executionParameters;
	}
}