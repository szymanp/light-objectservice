<?php
namespace Light\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedResource;
use Szyman\ObjectService\Service\ExecutionParameters;

/**
 * Returns a resource object.
 */
abstract class ResourceSpecification
{
	/**
	 * Returns the resource described by this specification.
	 * @param ExecutionParameters $parameters
	 * @return ResolvedResource
	 */
	abstract public function resolve(ExecutionParameters $parameters);
}
