<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectAccess\Type\ComplexType;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;
use Szyman\Exception\InvalidArgumentException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Response\DataSerializer;

class DefaultResponseContentTypeMapTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException Szyman\Exception\InvalidArgumentException
	 */
	public function testInvalidContentType()
	{
		$map = new DefaultResponseContentTypeMap();
		$map->set(
			$this->getMockBuilder(ComplexType::class)->getMock(),
			$this->getMockBuilder(DataSerializer::class)->getMock(),
			"hello world");
	}
	
	public function testMap()
	{
		$setup = Setup::create();
		$typeHelper = $setup->getTypeRegistry()->getComplexTypeHelper(Post::class);

		$map = new DefaultResponseContentTypeMap();
		$map->set(
			$typeHelper->getType(),
			$serializer = $this->getMockBuilder(DataSerializer::class)->getMock(),
			"application/hal+json");

		$resource = new ResolvedObject($typeHelper, new \stdClass(), EmptyResourceAddress::create(), Origin::unavailable());

		$this->assertEquals("application/hal+json", $map->getContentType($resource, $serializer));
		$this->assertNull($map->getContentType($resource, $this->getMockBuilder(DataSerializer::class)->getMock()));
		
	}
}
