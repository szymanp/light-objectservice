<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Service\Util\DefaultObjectProvider;

class EndpointTest extends \PHPUnit_Framework_TestCase
{
	public function testInstantiation()
	{
		$typeProvider = new DefaultTypeProvider();
		$typeRegistry = new TypeRegistry($typeProvider);
		$objectProvider = new DefaultObjectProvider($typeRegistry);
		$endpoint = Endpoint::create("//", $objectProvider);

		$this->assertInstanceOf(Endpoint::class, $endpoint);
		$this->assertSame($typeRegistry, $endpoint->getTypeRegistry());
		$this->assertEquals("//", $endpoint->getUrl());
	}
}
