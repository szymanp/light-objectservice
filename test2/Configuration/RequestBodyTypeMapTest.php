<?php
namespace Szyman\ObjectService\Configuration;

use Szyman\ObjectService\Service\RequestBodyType;
use Szyman\ObjectService\Configuration\Util\DefaultRequestBodyTypeMap;

class RequestBodyTypeMapTest extends \PHPUnit_Framework_TestCase
{
	const ACTION = 'application/vnd+objectservice.action+json';

	public function testDefaultRequestBodyTypeMap()
	{
		$map = new DefaultRequestBodyTypeMap();
		$map[self::ACTION] = RequestBodyType::get(RequestBodyType::ACTION);
		
		$this->assertNull($map->getRequestBodyType('application/json'));
		$this->assertSame(RequestBodyType::get(RequestBodyType::ACTION), $map->getRequestBodyType(self::ACTION));
	}
}
