<?php
namespace Light\ObjectService\Json\Request;
use Szyman\ObjectService\Service\ExecutionParameters;

/**
 * Base class for readers.
 */
abstract class Reader
{
	/** @var ExecutionParameters */
	protected $executionParameters;

	public function __construct(ExecutionParameters $executionParameters)
	{
		$this->executionParameters = $executionParameters;
	}

	/**
	 * @return ExecutionParameters
	 */
	public function getExecutionParameters()
	{
		return $this->executionParameters;
	}
}