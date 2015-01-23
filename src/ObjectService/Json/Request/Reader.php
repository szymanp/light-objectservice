<?php
namespace Light\ObjectService\Json\Request;
use Light\ObjectService\Resource\Operation\ExecutionParameters;

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