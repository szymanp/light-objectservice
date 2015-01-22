<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectAccess\Resource\ResolvedResource;

/**
 * An operation encapsulates the parameters and the logic of an action to be performed on a resource.
 * 
 */
abstract class Operation
{
	/**
	 * Executes the operation.
	 * @param ResolvedResource		$resource
	 * @param ExecutionParameters 	$parameters
	 */
	abstract public function execute(ResolvedResource $resource, ExecutionParameters $parameters);
}
