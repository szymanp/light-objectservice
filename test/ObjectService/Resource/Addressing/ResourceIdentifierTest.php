<?php
namespace ObjectService\Resource\Addressing;

use Light\ObjectService\Resource\Addressing\ResourceIdentifier;

class ResourceIdentifierTest extends \PHPUnit_Framework_TestCase
{
	public function testCreation()
	{
		$url = "http://example.org/endpoint/resource";
		$resourceIdentifier = ResourceIdentifier::create($url);
		$this->assertEquals($url, $resourceIdentifier->getUrl());
	}
}
 