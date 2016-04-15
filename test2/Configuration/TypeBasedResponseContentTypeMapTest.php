<?php
namespace Szyman\ObjectService\Configuration;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;
use Szyman\ObjectService\Configuration\Util\TypeBasedResponseContentTypeMap;
use Szyman\ObjectService\Response\DataSerializer;

class TypeBasedResponseContentTypeMapTest extends \PHPUnit_Framework_TestCase
{
	public function testMapWithOneValue()
	{
		$setup = Setup::create();
		$typeHelper = $setup->getTypeRegistry()->getComplexTypeHelper(Post::class);

		$map = new TypeBasedResponseContentTypeMap();
		$map->addClass(Post::class, "JSON", "application/vnd.post+json");

		$serializer1 = $this->getMockBuilder(DataSerializer::class)->getMock();
		$serializer1->method("getFormatName")->willReturn('JSON');

		$serializer2 = $this->getMockBuilder(DataSerializer::class)->getMock();
		$serializer2->method("getFormatName")->willReturn('XML');

		$resource = new ResolvedObject($typeHelper, new Post(), EmptyResourceAddress::create(), Origin::unavailable());

		$this->assertEquals("application/vnd.post+json", $map->getContentType($resource, $serializer1));
		$this->assertNull($map->getContentType($resource, $serializer2));
	}
}
