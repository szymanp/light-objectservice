<?php
namespace Light\ObjectService\Json\Request;

use Light\ObjectAccess\Resource\Origin_Unavailable;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectService\Resource\ExistingResourceSpecification;
use Light\ObjectService\Resource\NewResourceSpecification;
use Light\ObjectService\TestData\Author;
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

	public function testNewResourceSpec()
	{
		$setup = Setup::create();

		$json = new \stdClass;
		$json->meta = new \stdClass;
		$json->meta->spec = "new";
		$json->meta->type = "php:" . Author::class;
		$json->data = new \stdClass();
		$json->data->name = "Chris Waltz";
		$json->data->age = 41;

		$reader = new ResourceSpecificationReader($setup->getExecutionParameters());
		$spec = $reader->read($json);

		$this->assertInstanceOf(NewResourceSpecification::class, $spec);

		$resource = $spec->resolve($setup->getExecutionParameters());
		$this->assertInstanceOf(ResolvedObject::class, $resource);
		$this->assertInstanceOf(Origin_Unavailable::class, $resource->getOrigin());
		// FIXME Should this address be set to some default "primary" address where the new resource can be accessed?
		$this->assertInstanceOf(EmptyResourceAddress::class, $resource->getAddress());

		$author = $resource->getValue();
		$this->assertEquals("Chris Waltz", $author->getName());
		$this->assertEquals(41, $author->getAge());
		$this->assertEquals(2020, $author->getId());
	}
}
