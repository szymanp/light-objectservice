<?php
namespace Light\ObjectService\Json\Request;

use Light\ObjectService\Resource\ExistingResourceSpecification;
use Light\ObjectService\TestData\Setup;

class ResourceSpecificationReaderTest extends \PHPUnit_Framework_TestCase
{
	public function testExistingResourceSpec()
	{
		$setup = Setup::create();

		$json = new \stdClass;
		$json->meta = new \stdClass;
		$json->meta->spec = "reference";
		$json->meta->href = "http://example.org/resources/max";

		$reader = new ResourceSpecificationReader($setup->getExecutionParameters());
		$spec = $reader->read($json);

		$this->assertInstanceOf(ExistingResourceSpecification::class, $spec);

		$resource = $spec->resolve($setup->getExecutionParameters());
		$this->assertNotNull($resource);
		$this->assertSame($setup->getDatabase()->getAuthor(1010), $resource->getValue());
	}
}
