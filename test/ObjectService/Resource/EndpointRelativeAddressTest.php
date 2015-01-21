<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectAccess\Query\Scope;
use Light\ObjectService\TestData\Setup;

class EndpointRelativeAddressTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
	}

	public function testEmptyAddress()
	{
		$addr = EndpointRelativeAddress::create($this->setup->getEndpoint());

		$this->assertSame($this->setup->getEndpoint(), $addr->getEndpoint());
		$this->assertTrue($addr->hasStringForm());
		$this->assertEquals($this->setup->getEndpoint()->getUrl(), $addr->getAsString());
		$this->assertEquals("", $addr->getLocalAddressAsString());
	}

	public function testAddressFromString()
	{
		// Note: local address wouldn't normally start with a / - but we want to test that this case works
		$addr = EndpointRelativeAddress::create($this->setup->getEndpoint(), "/resources/employee/12");

		$this->assertTrue($addr->hasStringForm());
		$this->assertEquals("resources/employee/12", $addr->getLocalAddressAsString());
		$this->assertEquals("http://example.org/resources/employee/12", $addr->getAsString());
	}

	/**
	 * @expectedException \Light\Exception\InvalidParameterValue
	 */
	public function testAppendInvalidElement()
	{
		$addr = EndpointRelativeAddress::create($this->setup->getEndpoint());
		$addr->appendElement("hello/world");
	}

	public function testAppendElement()
	{
		$addr = EndpointRelativeAddress::create($this->setup->getEndpoint());
		$newAddr = $addr->appendElement("hello");

		$this->assertEquals("http://example.org/", $addr->getAsString());
		$this->assertEquals("http://example.org/hello", $newAddr->getAsString());
	}

	public function testAppendKeyScope()
	{
		$addr = EndpointRelativeAddress::create($this->setup->getEndpoint(), "job");
		$newAddr = $addr->appendScope(Scope::createWithKey(123));

		$this->assertEquals("http://example.org/job/123", $newAddr->getAsString());
	}

	public function testAppendIndexScope()
	{
		$addr = EndpointRelativeAddress::create($this->setup->getEndpoint(), "job");
		$newAddr = $addr->appendScope(Scope::createWithIndex(10));

		$this->assertFalse($newAddr->hasStringForm());
		$this->assertNull($newAddr->getAsString());
	}

	public function testAppendEmptyScope()
	{
		$addr = EndpointRelativeAddress::create($this->setup->getEndpoint(), "job");
		$newAddr = $addr->appendScope(Scope::createEmptyScope());

		$this->assertEquals("http://example.org/job/", $newAddr->getAsString());
	}

	public function testGetPathElements()
	{
		$addr = EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/job");
		$addr = $addr->appendScope($scope = Scope::createWithKey(123));
		$addr = $addr->appendElement("title");

		$this->assertEquals(array("resources", "job", $scope, "title"), $addr->getPathElements());
	}

}
