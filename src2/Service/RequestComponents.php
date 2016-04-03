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
	private $subjectResource, $requestUriResource;
	/** @var RequestHandler */
	private $requestHandler;
	/** @var ResponseCreator */
	private $responseCreator;
	/** @var EndpointRelativeAddress */
	private $endpointAddress;
	/** @var RequestBodyDeserializer */
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
			$rc->subjectResource 	= $vals->subjectResource;
			$rc->requestUriResource = $vals->requestUriResource;
			$rc->requestHandler  	= $vals->requestHandler;
			$rc->responseCreator 	= $vals->responseCreator;
			$rc->endpointAddress 	= $vals->endpointAddress;
			$rc->deserializer		= $vals->deserializer;
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
	 * Returns the resource that corresponds to the request-uri.
	 * @return ResolvedResource	A resource, if the request-uri identified an existing resource; otherwise, NULL.
	 */
	public function getRequestUriResource()
	{
		return $this->requestUriResource;
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
	 * Returns the endpoint-relative address of the resource at the request-uri.
	 * This address will usually correspond to the subject resource, but in some cases may be different.
	 * @return EndpointRelativeAddress
	 */
	public function getEndpointAddress()
	{
		return $this->endpointAddress;
	}

	/**
	 * Returns the deserializer that is compatible with the format of the request-body.
	 * @return RequestBodyDeserializer	A deserializer, if the request-body is set; otherwise, NULL.
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
	 * @param ResolvedResource $requestUriResource
	 * @return $this
	 */
	public function requestUriResource(ResolvedResource $requestUriResource)
	{
		$this->values->requestUriResource = $requestUriResource;
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
	 * @param RequestBodyDeserializer $requestBodyDeserializer
	 * @return $this
	 */
	public function deserializer(RequestBodyDeserializer $requestBodyDeserializer)
	{
		$this->values->deserializer = $requestBodyDeserializer;
		return $this;
	}
	
	/**
	 * Builds a new <kbd>RequestComponents</kbd> object.
	 * @return RequestComponents
	 */
	public function build()
	{
		// Check that mandatory arguments are specified.
		$mandatory = ['subjectResource', 'requestHandler', 'endpointAddress', 'responseCreator', 'requestHandler'];
		foreach($mandatory as $name)
		{
			if (is_null($this->values->$name)) throw new \LogicException("$name not set");
		}

		$fn = $this->fn;
		return $fn($this->values);
	}
}
