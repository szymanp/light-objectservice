<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Mockup\EndpointSetup;
use Light\ObjectService\Resource\Addressing\ResourceIdentifier;
use Light\ObjectService\Service\Request\RequestObject;
use Light\ObjectService\Service\Response\DataCollection;
use Light\ObjectService\Service\Response\DataObject;
use Light\ObjectService\Service\Util\InvocationParametersObject;

class InvocationTest extends \PHPUnit_Framework_TestCase
{
	/** @var EndpointSetup */
	private $endpointSetup;

	protected function setUp()
	{
		parent::setUp();

		$this->endpointSetup = new EndpointSetup();
	}

	public function testReadResource()
	{
		$parameters = new InvocationParametersObject();
		$parameters->copyFrom($this->endpointSetup->getExecutionParameters());

		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts/141"));

		$response = new MockupResponse();

		$invocation = new Invocation($parameters, $request, $response);
		$invocation->invoke();

		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
		$this->assertInstanceOf(DataObject::class, $response->result);

		$data = $response->result->getData();
		$this->assertEquals(141, $data->id);
		$this->assertEquals("This is my first post", $data->title);
		$this->assertEquals("this-is-my-first-post", $data->compact_title);
		$this->assertInstanceOf(DataCollection::class, $data->tags);
	}


}
 