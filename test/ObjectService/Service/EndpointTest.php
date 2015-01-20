<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Resource\Addressing\RelativeAddress;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Service\Util\DefaultObjectProvider;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\Setup;

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

	public function testFindResource()
	{
		$setup = Setup::create();
		$objectProvider = new DefaultObjectProvider($setup->getTypeRegistry());
		$endpoint = Endpoint::create("http://example.org/", $objectProvider);
		$objectProvider->setEndpoint($endpoint);

		$author = new Author(1010, "John Doe");
		$objectProvider->publishValue("names/John-Doe", $author);

		$result = $endpoint->findResource(array("names", "John-Doe", "age"));

		$this->assertInstanceOf(RelativeAddress::class, $result);
		$this->assertSame($objectProvider->getResource("names/John-Doe"), $result->getSourceResource());
		$this->assertEquals(array("age"), $result->getPathElements());

		$this->assertNull($endpoint->findResource(array("unknown", "path")));
	}
}
