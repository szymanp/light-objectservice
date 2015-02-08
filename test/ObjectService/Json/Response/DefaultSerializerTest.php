<?php
namespace Light\ObjectService\Json\Response;

use Light\ObjectService\Formats\Json\Serializers\DefaultSerializer;
use Light\ObjectService\Resource\Projection\Projector;
use Light\ObjectService\TestData\Setup;

class DefaultSerializerTest extends \PHPUnit_Framework_TestCase
{
	public function testSimpleObject()
	{
		$setup = Setup::create();

		$max = $setup->getEndpointRegistry()->getResource("http://example.org/resources/max");

		$projector = new Projector();
		$serializer = new DefaultSerializer();
		$stdobj  = $serializer->serializeToObject($projector->project($max));

		$json = json_encode($stdobj, JSON_PRETTY_PRINT);
		$this->assertJsonStringEqualsJsonFile(dirname(__FILE__) . "/DefaultSerializerTest.testSimpleObject.json", $json);
	}
}
