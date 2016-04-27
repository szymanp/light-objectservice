<?php
namespace Szyman\ObjectService\Configuration;
use Light\ObjectService\Service\EndpointRegistry;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestHandlerFactory;
use Szyman\ObjectService\Service\ResponseCreatorFactory;
use Szyman\ObjectService\Service\TransactionFactory;
use Psr\Log\LoggerInterface;

/**
 * Configuration settings.
 */
interface Configuration
{
	/**
	 * Returns the endpoint registry.
	 * @return EndpointRegistry
	 */
	public function getEndpointRegistry();

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

	/**
	 * Returns a TransactionFactory for transaction management.
	 * @return TransactionFactory
	 */
	public function getTransactionFactory();
    
    /**
     * Returns a logger to be used in the ObjectService context.
     * @return LoggerInterface
     */
    public function getLogger();
}
