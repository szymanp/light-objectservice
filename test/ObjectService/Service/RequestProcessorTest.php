<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Json\Request\Operation\AppendOperationReader;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Projection\DataCollection;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Resource\Projection\DataObject;
use Light\ObjectService\Service\Util\SettableRequest;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;

class RequestProcessorTest extends \PHPUnit_Framework_TestCase
{
	public function testMockRequest()
	{
		$setup = Setup::create();

		$request = new SettableRequest();
		$request->setResourceAddress(EndpointRelativeAddress::create($setup->getEndpoint(), "resources/max"));

		$requestProcessor = new RequestProcessor($setup->getExecutionParameters(), $request);
		$requestProcessor->process();

		$this->assertFalse($requestProcessor->hasException());
		$this->assertTrue($requestProcessor->hasEntity());
		$this->assertInstanceOf(DataObject::class, $requestProcessor->getEntity());
	}

	public function testAppendToCollection()
	{
		$setup = Setup::create();
		$count = count($setup->getDatabase()->getPosts());

		// Setup the request from JSON
		$request = new SettableRequest();
		$request->setResourceAddress(EndpointRelativeAddress::create($setup->getEndpoint(), "collections/post"));

		$json = new \stdClass;
		$json->meta = new \stdClass;
		$json->meta->spec = "new";
		$json->meta->type = "php:" . Post::class;

		$json->data = new \stdClass();
		$json->data->title = "My new post";

		$reader = new AppendOperationReader($setup->getExecutionParameters());
		$request->addOperation($reader->read($json));

		// Run the request
		$requestProcessor = new RequestProcessor($setup->getExecutionParameters(), $request);
		$requestProcessor->disableErrorHandling();
		$requestProcessor->process();

		// Check that the element was appended
		$this->assertFalse($requestProcessor->hasException(), "An exception was found");
		$this->assertTrue($requestProcessor->hasEntity(), "No entity was found");
		$this->assertInstanceOf(DataCollection::class, $requestProcessor->getEntity());
		$this->assertEquals($count+1, count($setup->getDatabase()->getPosts()));
	}
}