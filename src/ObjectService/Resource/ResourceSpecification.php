<?php
namespace Light\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedResource;
use Szyman\ObjectService\Service\ExecutionEnvironment;

/**
 * Returns a resource object.
 */
abstract class ResourceSpecification
{
	/**
	 * Returns the resource described by this specification.
	 * @param ExecutionEnvironment $parameters
	 * @return ResolvedResource
	 */
	abstract public function resolve(ExecutionEnvironment $parameters);
}
