<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectService\Service\EndpointRegistry;
use Szyman\ObjectService\Configuration\RequestBodyTypeMap;
use Szyman\ObjectService\Configuration\Configuration;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestHandlerFactory;
use Szyman\ObjectService\Service\ResponseCreatorFactory;

final class DefaultConfiguration implements Configuration
{
	/** @var EndpointRegistry */
	private $endpointRegistry;
	/** @var RequestBodyTypeMap */
	private $requestBodyTypeMap;
	/** @var RequestBodyDeserializerFactory */
	private $requestBodyDeserializerFactory;
	/** @var RequestHandlerFactory */
	private $requestHandlerFactory;
	/** @var ResponseCreatorFactory */
	private $responseCreatorFactory;

	private function __construct()
	{
		// Private construtor - cannot be instantiated by itself, use the static methods instead.
	}
	
	/**
	 * Returns a new builder for building a <kbd>DefaultConfiguration</kbd> object.
	 * @return DefaultConfiguration_Builder
	 */
	public static function newBuilder()
	{
		return new DefaultConfiguration_Builder(function(\stdClass $vals)
		{
			$dc = new self;
			$dc->endpointRegistry				= $vals->endpointRegistry;
			$dc->requestBodyTypeMap				= $vals->requestBodyTypeMap;
			$dc->requestBodyDeserializerFactory	= $vals->requestBodyDeserializerFactory;
			$dc->requestHandlerFactory			= $vals->requestHandlerFactory;
			$dc->responseCreatorFactory			= $vals->responseCreatorFactory;
			return $dc;
		});
	}

	/**
	 * Returns the endpoint registry.
	 * @return EndpointRegistry
	 */
	public function getEndpointRegistry()
	{
		return $this->endpointRegistry;
	}


	/**
	 * Returns a RequestBodyTypeMap to be used with the RestRequestReader.
	 * @return RequestBodyTypeMap
	 */
	public function getRequestBodyTypeMap()
	{
		return $this->requestBodyTypeMap;
	}

	/**
	 * Returns a RequestBodyDeserializerFactory used for instantiating deserializers.
	 * @return RequestBodyDeserializerFactory
	 */
	public function getRequestBodyDeserializerFactory()
	{
		return $this->requestBodyDeserializerFactory;
	}

	/**
	 * Returns a RequestHandlerFactory.
	 * @return RequestHandlerFactory
	 */
	public function getRequestHandlerFactory()
	{
		return $this->requestHandlerFactory;
	}

	/**
	 * Returns a ResponseCreatorFactory for instantiating response creators.
	 * @return ResponseCreatorFactory
	 */
	public function getResponseCreatorFactory()
	{
		return $this->responseCreatorFactory;
	}
}

/**
 * A builder for DefaultConfiguration objects.
 */
final class DefaultConfiguration_Builder
{
	private $values, $fn;
	
	public function __construct(\Closure $fn)
	{
		$this->values = new \stdClass;
		$this->fn = $fn;
	}

	public function endpointRegistry(EndpointRegistry $endpointRegistry)
	{
		$this->values->endpointRegistry = $endpointRegistry;
		return $this;
	}
	
	/**
	 * @param RequestBodyTypeMap $requestBodyTypeMap
	 * @return $this
	 */
	public function requestBodyTypeMap(RequestBodyTypeMap $requestBodyTypeMap)
	{
		$this->values->requestBodyTypeMap = $requestBodyTypeMap;
		return $this;
	}
	
	/**
	 * @param RequestBodyDeserializerFactory $requestBodyDeserializerFactory
	 * @return $this
	 */
	public function requestBodyDeserializerFactory(RequestBodyDeserializerFactory $requestBodyDeserializerFactory)
	{
		$this->values->requestBodyDeserializerFactory = $requestBodyDeserializerFactory;
		return $this;
	}

	/**
	 * @param RequestHandlerFactory $requestHandlerFactory
	 * @return $this
	 */	
	public function requestHandlerFactory(RequestHandlerFactory $requestHandlerFactory)
	{
		$this->values->requestHandlerFactory = $requestHandlerFactory;
		return $this;
	}
	
	/**
	 * @param ResponseCreatorFactory $responseCreatorFactory
	 * @return $this
	 */
	public function responseCreatorFactory(ResponseCreatorFactory $responseCreatorFactory)
	{
		$this->values->responseCreatorFactory = $responseCreatorFactory;
		return $this;
	}

	/**
	 * Builds a new <kbd>DefaultConfiguration</kbd> object.
	 * @return DefaultConfiguration
	 */
	public function build()
	{
		// Check that mandatory arguments are specified.
		$mandatory = ['endpointRegistry', 'requestBodyTypeMap', 'requestBodyDeserializerFactory', 'requestHandlerFactory', 'responseCreatorFactory'];
		foreach($mandatory as $name)
		{
			if (is_null($this->values->$name)) throw new \LogicException("$name not set");
		}

		$fn = $this->fn;
		return $fn($this->values);
	}
}
