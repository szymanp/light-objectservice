<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectService\Service\EndpointRegistry;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestHandlerFactory;
use Szyman\ObjectService\Service\ResponseCreatorFactory;

class DefaultConfigurationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException LogicException
	 */
	public function testIncompleteBuilder()
	{
		$dcb = DefaultConfiguration::newBuilder();
		$dcb->endpointRegistry(new EndpointRegistry());
		$dcb->build();
	}

	public function testBuilder()
	{
		$dcb = DefaultConfiguration::newBuilder();

		$dcb->endpointRegistry($endpointRegistry = new EndpointRegistry());
		$dcb->requestBodyTypeMap(new DefaultRequestBodyTypeMap());
		$dcb->requestBodyDeserializerFactory($this->getMockBuilder(RequestBodyDeserializerFactory::class)->getMock());
		$dcb->requestHandlerFactory($this->getMockBuilder(RequestHandlerFactory::class)->getMock());
		$dcb->responseCreatorFactory($this->getMockBuilder(ResponseCreatorFactory::class)->getMock());

		$dc = $dcb->build();
		$this->assertInstanceOf(DefaultConfiguration::class, $dc);
		
		$this->assertSame($endpointRegistry, $dc->getEndpointRegistry());
	}	
}
