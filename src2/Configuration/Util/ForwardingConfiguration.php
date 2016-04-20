<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectService\Service\EndpointRegistry;
use Szyman\ObjectService\Configuration\RequestBodyTypeMap;
use Szyman\ObjectService\Configuration\Configuration;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestHandlerFactory;
use Szyman\ObjectService\Service\ResponseCreatorFactory;
use Szyman\ObjectService\Service\TransactionFactory;

/**
 * A configuration that forwards all calls to an inner configuration object.
 *
 * This class can be used as a base for defining new configurations with an extended set of parameters.
 */
abstract class ForwardingConfiguration implements Configuration
{
	/** @var Configuration */
	private $inner;

	public function __construct(Configuration $inner)
	{
		$this->inner = $inner;
	}

	/** @inheritdoc */	
	public function getEndpointRegistry()
	{
		return $this->inner->getEndpointRegistry();
	}


	/** @inheritdoc */	
	public function getRequestBodyTypeMap()
	{
		return $this->inner->getRequestBodyTypeMap();
	}

	/** @inheritdoc */	
	public function getRequestBodyDeserializerFactory()
	{
		return $this->inner->getRequestBodyDeserializerFactory();
	}

	/** @inheritdoc */	
	public function getRequestHandlerFactory()
	{
		return $this->inner->getRequestHandlerFactory();
	}

	/** @inheritdoc */	
	public function getResponseCreatorFactory()
	{
		return $this->inner->getResponseCreatorFactory();
	}

	/** @inheritdoc */	
	public function getTransactionFactory()
	{
		return $this->inner->getTransactionFactory();
	}
}
