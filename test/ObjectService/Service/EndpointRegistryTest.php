<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Szyman\ObjectService\Configuration\Endpoint;
use Szyman\ObjectService\Configuration\Util\DefaultObjectProvider;
use Light\ObjectService\TestData\Setup;

class EndpointRegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetResourceAddress()
	{
		$setup = Setup::create();

		$typeProvider = new DefaultTypeProvider();
		$objectProvider = new DefaultObjectProvider();

		$registry = new EndpointRegistry();
		$endpoint1 = Endpoint::create("http://example.org/", $objectProvider, $typeProvider);
		$endpoint2 = Endpoint::create("http://example.com", $objectProvider, $typeProvider);

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
