<?php
namespace Light\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\TestData\Setup;

class ResourceSpecificationTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
	}

	public function testExistingResourceSpecificationWithFullAddress()
	{
		$address = EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/max");

		$spec = new ExistingResourceSpecification($address);
		$resource = $spec->resolve($this->setup->getExecutionParameters());

		$this->assertInstanceOf(ResolvedObject::class, $resource);
		$this->assertSame($this->setup->getDatabase()->getAuthor(1010), $resource->getValue());
		$this->assertEquals("http://example.org/resources/max", $resource->getAddress()->getAsString());
	}
}
