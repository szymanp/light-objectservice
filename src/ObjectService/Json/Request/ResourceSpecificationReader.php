<?php
namespace Light\ObjectService\Json\Request;

use Light\ObjectService\Resource\Operation\ExecutionParameters;

class ResourceSpecificationReader
{
	/** @var ExecutionParameters */
	private $executionParameters;

	public function __construct(ExecutionParameters $executionParameters)
	{
		$this->executionParameters = $executionParameters;
	}

	public function read(\stdClass $json)
	{
		// TODO
	}
}