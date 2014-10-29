<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectService\Resource\ResolvedValue;

/**
 * A base class for describing requested operations on resources.
 * 
 */
abstract class Operation
{
	/**
	 * Executes the operation.
	 * @param ResolvedValue			$resource
	 * @param ExecutionParameters 	$params
	 * @return ResolvedValue Result resource
	 */
	abstract public function execute(ResolvedValue $resource, ExecutionParameters $params);
}
