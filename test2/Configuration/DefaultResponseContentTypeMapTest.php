<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Resource\ResolvedResource;
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
			$this->getMockBuilder(ResolvedResource::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(DataSerializer::class)->getMock(),
			"hello world");
	}
	
	public function testMap()
	{
		$map = new DefaultResponseContentTypeMap();
		$map->set(
			$resource = $this->getMockBuilder(ResolvedResource::class)->disableOriginalConstructor()->getMock(),
			$serializer = $this->getMockBuilder(DataSerializer::class)->getMock(),
			"application/hal+json");
			
		$this->assertEquals("application/hal+json", $map->getContentType($resource, $serializer));
		$this->assertNull($map->getContentType($resource, $this->getMockBuilder(DataSerializer::class)->getMock()));
		
	}
}
