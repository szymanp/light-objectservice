<?php
namespace Szyman\ObjectService\Configuration;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestHandlerFactory;
use Szyman\ObjectService\Service\ResponseCreatorFactory;

/**
 * Configuration for the RestRequestReader.
 */
interface RestRequestReaderConfiguration
{
	/**
	 * Returns a RequestBodyTypeMap to be used with the RestRequestReader.
	 * @return RequestBodyTypeMap
	 */
	public function getRequestBodyTypeMap();

	/**
	 * Returns a RequestBodyDeserializerFactory used for instantiating deserializers.
	 * @return RequestBodyDeserializerFactory
	 */
	public function getRequestBodyDeserializerFactory();

	/**
	 * Returns a RequestHandlerFactory.
	 * @return RequestHandlerFactory
	 */
	public function getRequestHandlerFactory();

	/**
	 * Returns a ResponseCreatorFactory for instantiating response creators.
	 * @return ResponseCreatorFactory
	 */
	public function getResponseCreatorFactory();
}
