<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectService\Service\EndpointRegistry;
use Light\ObjectService\TestData\DummyTransactionFactory;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestHandlerFactory;
use Szyman\ObjectService\Service\ResponseCreatorFactory;
use Psr\Log\NullLogger;

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
		$dcb->transactionFactory(new DummyTransactionFactory());

		$dc = $dcb->build();
		$this->assertInstanceOf(DefaultConfiguration::class, $dc);

        $this->assertInstanceOf(NullLogger::class, $dc->getLogger());
		$this->assertSame($endpointRegistry, $dc->getEndpointRegistry());
	}
}
