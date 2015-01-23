<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedCollectionValue;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\ExistingResourceSpecification;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;

class UpdateOperationTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
	}

	public function testUpdate()
	{
		$authorSpec = new ExistingResourceSpecification(EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/max"));

		$updateOperation = new UpdateOperation();
		$updateOperation->setValue("title", "Updated post");
		$updateOperation->setResource("author", $authorSpec);

		$resource = ResolvedValue::create(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class),
			$post = $this->setup->getDatabase()->getPost(4042),
			EmptyResourceAddress::create(),
			Origin::unavailable());

		$updateOperation->execute($resource, $this->setup->getExecutionParameters());

		$this->assertEquals("Updated post", $post->getTitle());
		$this->assertSame($this->setup->getDatabase()->getAuthor(1010), $post->getAuthor());
	}

	public function testMultipleUpdate()
	{
		$resource = $this->setup->getEndpointRegistry()->getResource("http://example.org/resources/max/posts/");
		$this->assertInstanceOf(ResolvedCollectionValue::class, $resource);

		$updateOperation = new UpdateOperation();
		$updateOperation->setValue("title", $newTitle = "Updated post!");
		$updateOperation->execute($resource, $this->setup->getExecutionParameters());

		$this->assertEquals($newTitle, $this->setup->getDatabase()->getPost(4040)->getTitle());
		$this->assertEquals($newTitle, $this->setup->getDatabase()->getPost(4041)->getTitle());
	}
}
