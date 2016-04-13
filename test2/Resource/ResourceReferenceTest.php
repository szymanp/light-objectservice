<?php
namespace Szyman\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;

class ResourceReferenceTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
	}

	public function testExistingResourceReferenceWithFullAddress()
	{
		$address = EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/max");

		$ref = new ExistingResourceReference($address);
		$resource = $ref->resolve($this->setup->getExecutionParameters());

		$this->assertInstanceOf(ResolvedObject::class, $resource);
		$this->assertSame($this->setup->getDatabase()->getAuthor(1010), $resource->getValue());
		$this->assertEquals("http://example.org/resources/max", $resource->getAddress()->getAsString());
	}

	public function testNewComplexResourceReference()
	{
		$representation = new KeyValueComplexValueRepresentation();
		$representation->setValue('title', 'A new post');

		$ref = new NewComplexResourceReference($this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class), $representation);
		$resource = $ref->resolve($this->setup->getExecutionParameters());

		$this->assertInstanceOf(ResolvedObject::class, $resource);
		$post = $resource->getValue();
		$this->assertInstanceOf(Post::class, $post);
		$this->assertEquals("A new post", $post->getTitle());
		$this->assertNull($post->getId());	// The post was not added to the collection, so it does not have an ID.
	}
}
