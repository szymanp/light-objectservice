<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Resource\Addressing\RelativeAddress;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Szyman\ObjectService\Configuration\Endpoint;
use Szyman\ObjectService\Configuration\Util\DefaultObjectProvider;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\Setup;

class EndpointTest extends \PHPUnit_Framework_TestCase
{
	public function testInstantiation()
	{
		$typeProvider = new DefaultTypeProvider();
		$objectProvider = new DefaultObjectProvider();
		$endpoint = Endpoint::create("//", $objectProvider, $typeProvider);

		$this->assertInstanceOf(Endpoint::class, $endpoint);
		$this->assertSame($typeProvider, $endpoint->getTypeRegistry()->getTypeProvider());
		$this->assertEquals("//", $endpoint->getPrimaryUrl());
	}

	public function testAlternativeUrls()
	{
		$typeProvider = new DefaultTypeProvider();
		$objectProvider = new DefaultObjectProvider();
		$endpoint = Endpoint::create("//", $objectProvider, $typeProvider);

		$endpoint->addAlternativeUrl("//alt1");
		$endpoint->addAlternativeUrl("//alt2");

		$this->assertEquals("//", $endpoint->getPrimaryUrl());
		$this->assertEquals(['//', '//alt1', '//alt2'], $endpoint->getUrls());
	}

	public function testFindResource()
	{
		$setup = Setup::create();

		$objectProvider = new DefaultObjectProvider();
		$typeProvider = $setup->getTypeProvider();

		$endpoint = Endpoint::create("http://example.org/", $objectProvider, $typeProvider);

		$author = new Author(1010, "John Doe");
		$objectProvider->publishValue("names/John-Doe", $author);

		$result = $endpoint->findResource(array("names", "John-Doe", "age"));

		$this->assertInstanceOf(RelativeAddress::class, $result);
		$this->assertSame($author, $result->getSourceResource()->getValue());
		$this->assertEquals(array("age"), $result->getPathElements());

		$this->assertNull($endpoint->findResource(array("unknown", "path")));
	}
}
