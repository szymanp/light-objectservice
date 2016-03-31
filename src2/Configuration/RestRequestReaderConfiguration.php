<?php
namespace Szyman\ObjectService\Configuration;

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
	
	public function getRequestBodyDeserializerFactory();
	
	public function getRequestHandlerFactory();
	
	public function getResponseCreatorFactory();
}
