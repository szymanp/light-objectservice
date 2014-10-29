<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Resource\Operation\ExecutionParameters;

/**
 * Returns a resource object.
 */
abstract class ResourceSpecification
{
	/**
	 * @param ExecutionParameters $parameters
	 * @return ResolvedValue
	 */
	abstract public function resolve(ExecutionParameters $parameters);
}
