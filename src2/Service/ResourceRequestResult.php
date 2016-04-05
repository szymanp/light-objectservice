<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedResource;

/**
 * A result from a <kbd>RequestHandler<kbd> that contains a single resource.
 */
class ResourceRequestResult implements RequestResult
{
	/** @var ResolvedResource */
	private $resource;
	
	public function __construct(ResolvedResource $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * Returns the resource that is the result of processing the request.
	 * @return ResolvedResource
	 */
	final public function getResource()
	{
		return $this->resource;
	}
}
