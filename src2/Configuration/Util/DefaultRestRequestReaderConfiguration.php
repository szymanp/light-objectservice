<?php
namespace Szyman\ObjectService\Configuration\Util;

use Szyman\ObjectService\Configuration\RequestBodyTypeMap;
use Szyman\ObjectService\Configuration\RestRequestReaderConfiguration;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestHandlerFactory;
use Szyman\ObjectService\Service\ResponseCreatorFactory;

final class DefaultRestRequestReaderConfiguration implements RestRequestReaderConfiguration
{
	/** @var RequestBodyTypeMap */
	private $requestBodyTypeMap;
	/** @var RequestBodyDeserializerFactory */
	private $requestBodyDeserializerFactory;
	/** @var RequestHandlerFactory */
	private $requestHandlerFactory;
	/** @var ResponseCreatorFactory */
	private $responseCreatorFactory;

	public function __construct()
	{
		// TODO
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
