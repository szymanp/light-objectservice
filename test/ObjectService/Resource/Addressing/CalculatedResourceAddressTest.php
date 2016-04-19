<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectAccess\Query\Scope;
use Light\ObjectService\TestData\Setup;
use Light\ObjectService\TestData\Post;

class CalculatedResourceAddressTest extends \PHPUnit_Framework_TestCase
{
	private $post, $staticClosure, $dynamicClosure;
	
	protected function setUp()
	{
		$this->post = $post = new Post();

		$endpoint = Setup::create()->getEndpoint();
		$endpointAddr = EndpointRelativeAddress::create($endpoint, "resources");

		$this->staticClosure = function() use ($endpointAddr)
		{
			return $endpointAddr->appendElement("john");
		};
		$this->dynamicClosure = function() use ($endpointAddr, $post)
		{
			$id = is_null($post->getId()) ? "none" : $post->getId();
			return $endpointAddr->appendElement("post")->appendElement($id);
		};
	}

	public function testDynamicValueWithStaticAddress()
	{
		$addr = CalculatedResourceAddress::staticAddress($this->dynamicClosure);
		$this->assertEquals('http://example.org/resources/post/none', $addr->getAsString());
		
		// Change the post ID - the address should not change
		$this->post->setId(123456);
		$this->assertEquals('http://example.org/resources/post/none', $addr->getAsString());
	}

	public function testStaticValueWithDynamicAddress()
	{
		$addr = CalculatedResourceAddress::dynamicAddress($this->staticClosure);
		
		$this->assertTrue($addr->hasStringForm());
		$this->assertEquals('http://example.org/resources/john', $addr->getAsString());
		// Run it again
		$this->assertEquals('http://example.org/resources/john', $addr->getAsString());
	}
	
	public function testDynamicValueWithDynamicAddress()
	{
		$addr = CalculatedResourceAddress::dynamicAddress($this->dynamicClosure);
		
		// With NULL id
		$this->assertTrue($addr->hasStringForm());
		$this->assertEquals('http://example.org/resources/post/none', $addr->getAsString());
		
		// With an integer id
		$this->post->setId(12345);
		$this->assertTrue($addr->hasStringForm());
		$this->assertEquals('http://example.org/resources/post/12345', $addr->getAsString());
	}


	public function testAppendElementToDynamicAddress()
	{
		$addr = CalculatedResourceAddress::dynamicAddress($this->dynamicClosure);

		$newAddr = $addr->appendElement("authors");
		
		// With NULL id
		$this->assertTrue($newAddr->hasStringForm());
		$this->assertEquals('http://example.org/resources/post/none/authors', $newAddr->getAsString());
		
		// With an integer id
		$this->post->setId(12345);
		$this->assertTrue($newAddr->hasStringForm());
		$this->assertEquals('http://example.org/resources/post/12345/authors', $newAddr->getAsString());
	}
}

