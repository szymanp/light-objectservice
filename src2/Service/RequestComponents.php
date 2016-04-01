<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;

/**
 * A container holding the decoded components of a request.
 */
final class RequestComponents
{
	/** @var ResolvedResource */
	private $subjectResource;
	/** @var RequestHandler */
	private $requestHandler;
	/** @var ResponseCreator */
	private $responseCreator;
	/** @var EndpointRelativeAddress */
	private $endpointAddress;
	/** @var */
	private $deserializer;
	
	private function __construct()
	{
		// Private construtor - cannot be instantiated by itself, use the builder instead.
	}
	
	/**
	 * Returns a new builder for building a <kbd>RequestComponents</kbd> object.
	 * @return RequestComponents_Builder
	 */
	public static function newBuilder()
	{
		return new RequestComponents_Builder(function($vals)
		{
			$rc = new self;
			$rc->subjectResource = $vals->subjectResource;
			return $rc;
		});
	}
	
	/**
	 * Returns the resource that is the subject of this request.
	 * Usually, this is the resource identified by the request-uri.
	 * @return ResolvedResource
	 */
	public function getSubjectResource()
	{
		return $this->subjectResource;
	}
}

/**
 * A builder for RequestComponents objects.
 */
final class RequestComponents_Builder
{
	private $values, $fn;
	
	public function __construct(\Closure $fn)
	{
		$this->values = new \stdClass;
		$this->fn = $fn;
	}

	public function subjectResource(ResolvedResource $subjectResource)
	{
		$this->values->subjectResource = $subjectResource;
		return $this;
	}
	
	/**
	 * Builds a new <kbd>RequestComponents</kbd> object.
	 * @return RequestComponents
	 */
	public function build()
	{
		$fn = $this->fn;
		return $fn($this->values);
	}
}
