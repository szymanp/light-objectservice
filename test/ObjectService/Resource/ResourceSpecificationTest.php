<?php
namespace Light\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Operation\UpdateOperation;
use Light\ObjectService\TestData\Post;
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

	public function testNewResourceSpecification()
	{
		$updateSpec = new UpdateOperation();
		$updateSpec->setValue("title", "A new post");
		$updateSpec->setResource("author", new ExistingResourceSpecification(EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/max")));

		$spec = new NewResourceSpecification($this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class), $updateSpec);
		$resource = $spec->resolve($this->setup->getExecutionParameters());

		$this->assertInstanceOf(ResolvedObject::class, $resource);
		$post = $resource->getValue();
		$this->assertInstanceOf(Post::class, $post);
		$this->assertEquals("A new post", $post->getTitle());
		$this->assertNull($post->getId());	// The post was not added to the collection, so it does not have an ID.
	}
}
