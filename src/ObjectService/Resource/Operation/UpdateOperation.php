<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectService\Resource\ResolvedValue;

class UpdateOperation extends Operation
{
	/** @var ResourceUpdateSpecification */
	private $resourceUpdateSpecification;

	/**
	 * Constructs a new UpdateOperation.
	 * @param ResourceUpdateSpecification $updateSpec
	 */
	public function __construct(ResourceUpdateSpecification $updateSpec)
	{
		$this->resourceUpdateSpecification = $updateSpec;
	}

	/**
	 * Executes the operation.
	 * @param ResolvedValue       $resource
	 * @param ExecutionParameters $params
	 * @return ResolvedValue Result resource
	 */
	public function execute(ResolvedValue $resource, ExecutionParameters $params)
	{
		$this->resourceUpdateSpecification->update($resource, $params);

		return $resource;
	}
}