<?php
namespace Szyman\ObjectService\Response;

class JsonDataSerializerTest extends \PHPUnit_Framework_TestCase
{
	public function testSerializeObject()
	{
		$o = new \stdClass();
		$o->name = "John Doe";
		$o->age  = 23;

		$ser = new JsonDataSerializer();
		$result = $ser->serializeData($o);
		$this->assertEquals('{"name":"John Doe","age":23}', $result);
	}

	public function testGetFormatName()
	{
		$ser = new JsonDataSerializer();
		$this->assertEquals("JSON", $ser->getFormatName());
	}
}
