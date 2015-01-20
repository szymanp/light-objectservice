<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Service\Util\DefaultObjectProvider;
use Light\ObjectService\TestData\Setup;

class EndpointRegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetResourceAddress()
	{
		$setup = Setup::create();
		$objectProvider = new DefaultObjectProvider($setup->getTypeRegistry());

		$registry = new EndpointRegistry();
		$endpoint1 = Endpoint::create("http://example.org/", $objectProvider);
		$endpoint2 = Endpoint::create("http://example.com", $objectProvider);

		$registry->addEndpoint($endpoint1);
		$registry->addEndpoint($endpoint2);

		$address = $registry->getResourceAddress("http://example.org/resource/path");
		$this->assertInstanceOf(EndpointRelativeAddress::class, $address);
		$this->assertSame($endpoint1, $address->getEndpoint());
		$this->assertEquals("resource/path", $address->getLocalAddressAsString());

		$address = $registry->getResourceAddress("http://example.com/resource/path");
		$this->assertInstanceOf(EndpointRelativeAddress::class, $address);
		$this->assertSame($endpoint2, $address->getEndpoint());
		$this->assertEquals("resource/path", $address->getLocalAddressAsString());

		$this->assertNull($registry->getResourceAddress("http://unknown.net"));
	}
}
