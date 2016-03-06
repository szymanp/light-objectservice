<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectAccess\Resource\ResolvedResource;
use Szyman\ObjectService\Service\ExecutionEnvironment;

/**
 * An operation encapsulates the parameters and the logic of an action to be performed on a resource.
 * 
 */
abstract class Operation
{
	/**
	 * Executes the operation.
	 * @param ResolvedResource		$resource
	 * @param ExecutionEnvironment 	$parameters
	 */
	abstract public function execute(ResolvedResource $resource, ExecutionEnvironment $parameters);
}
