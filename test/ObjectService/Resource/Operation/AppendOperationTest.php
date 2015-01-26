<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;

class AppendOperationTest extends \PHPUnit_Framework_TestCase
{
	public function testAppend()
	{
		$setup = Setup::create();

		$post = new Post(1234);
		$postResource = new ResolvedObject($setup->getTypeRegistry()->getComplexTypeHelper(Post::class), $post, EmptyResourceAddress::create(), Origin::unavailable());

		$coll = $setup->getEndpointRegistry()->getResource("http://example.org/resources/max/posts");

		$appendOperation = new AppendOperation();
		$appendOperation->setElementResource($postResource);
		$appendOperation->execute($coll, $setup->getExecutionParameters());

		$this->assertSame($post, $setup->getDatabase()->getPost(1234));
		$this->assertSame($setup->getDatabase()->getAuthor(1010), $post->getAuthor());
	}
}
