<?php
namespace Light\ObjectService\Json\Request;

use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectService\Json\Request\Operation\AppendOperationReader;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;

class AppendOperationReaderTest extends \PHPUnit_Framework_TestCase
{
	public function testRead()
	{
		$setup = Setup::create();

		$json = new \stdClass;
		$json->meta = new \stdClass;
		$json->meta->spec = "new";
		$json->meta->type = "php:" . Post::class;

		$reader = new AppendOperationReader($setup->getExecutionParameters());
		$operation = $reader->read($json);

		$resource = $operation->getElementResource();
		$this->assertInstanceOf(ResolvedObject::class, $resource);
	}

	public function testReadAndExecute()
	{
		$setup = Setup::create();

		$count = count($setup->getDatabase()->getPosts());

		$subjectResource = $setup->getEndpointRegistry()->getResource("http://example.org/collections/post");

		$json = new \stdClass;
		$json->meta = new \stdClass;
		$json->meta->spec = "new";
		$json->meta->type = "php:" . Post::class;

		$json->data = new \stdClass();
		$json->data->title = "My new post";

		$reader = new AppendOperationReader($setup->getExecutionParameters());
		$operation = $reader->read($json);

		$operation->execute($subjectResource, $setup->getExecutionParameters());

		// Check that the element was appended
		$this->assertEquals($count+1, count($setup->getDatabase()->getPosts()));
	}
}
