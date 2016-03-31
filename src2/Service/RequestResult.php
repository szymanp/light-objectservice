<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedResource;

/**
 * A result from a <kbd>RequestHandler<kbd>.
 *
 * This interface can be extended to contain more specialized results.
 */
interface RequestResult
{
	/**
	 * Returns the resource that is the result of processing the request.
	 *
	 * @return ResolvedResource	A ResolvedResource, if the request resulted in a resource;
	 *							otherwise, NULL.
	 */
	public function getResource();
}
