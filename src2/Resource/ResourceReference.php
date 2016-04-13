<?php
namespace Szyman\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedResource;
use Szyman\ObjectService\Service\ExecutionEnvironment;

/**
 * A resolvable reference to a resource.
 */
abstract class ResourceReference
{
	/**
	 * Returns the referenced resource.
	 * @param ExecutionEnvironment $environment
	 * @throws ResourceReferenceException	Thrown if the reference could not be resolved to a resource.
	 *                                      The exception should provide the details of the problem, and possibly
	 *                                    	include a sub-exception with the details.
	 * @return ResolvedResource
	 */
	abstract public function resolve(ExecutionEnvironment $environment);
}