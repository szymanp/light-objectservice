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
		return new RequestComponents_Builder(function(\stdClass $vals)
		{
			$rc = new self;
			$rc->subjectResource = $vals->subjectResource;
			$rc->requestHandler  = $vals->requestHandler;
			$rc->responseCreator = $vals->responseCreator;
			$rc->endpointAddress = $vals->endpointAddress;
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

	/**
	 * @return RequestHandler
	 */
	public function getRequestHandler()
	{
		return $this->requestHandler;
	}

	/**
	 * @return ResponseCreator
	 */
	public function getResponseCreator()
	{
		return $this->responseCreator;
	}

	/**
	 * @return EndpointRelativeAddress
	 */
	public function getEndpointAddress()
	{
		return $this->endpointAddress;
	}

	/**
	 * @return mixed
	 */
	public function getDeserializer()
	{
		return $this->deserializer;
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

	/**
	 * @param ResolvedResource $subjectResource
	 * @return $this
	 */
	public function subjectResource(ResolvedResource $subjectResource)
	{
		$this->values->subjectResource = $subjectResource;
		return $this;
	}

	/**
	 * @param RequestHandler $requestHandler
	 * @return $this
	 */
	public function requestHandler(RequestHandler $requestHandler)
	{
		$this->values->requestHandler = $requestHandler;
		return $this;
	}

	/**
	 * @param ResponseCreator $responseCreator
	 * @return $this
	 */
	public function responseCreator(ResponseCreator $responseCreator)
	{
		$this->values->responseCreator = $responseCreator;
		return $this;
	}

	/**
	 * @param EndpointRelativeAddress $endpointRelativeAddress
	 * @return $this
	 */
	public function endpointAddress(EndpointRelativeAddress $endpointRelativeAddress)
	{
		$this->values->endpointAddress = $endpointRelativeAddress;
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
