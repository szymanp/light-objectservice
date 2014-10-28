<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Resource\ResolvedValue;

class ExistingResourceSpecification extends ResourceSpecification
{
	/** @var ResourceIdentifier */
	private $resourceIdentifier;

	/**
	 * @param ExecutionParameters $parameters
	 * @return ResolvedValue
	 */
	public function resolve(ExecutionParameters $parameters)
	{
		// TODO: Implement resolve() method.
	}
}