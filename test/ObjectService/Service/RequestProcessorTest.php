<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Resource\Projection\DataObject;
use Light\ObjectService\Service\Util\SettableRequest;
use Light\ObjectService\TestData\Setup;

class RequestProcessorTest extends \PHPUnit_Framework_TestCase
{
	public function testMockRequest()
	{
		$setup = Setup::create();

		$request = new SettableRequest();
		$request->setResourceAddress(EndpointRelativeAddress::create($setup->getEndpoint(), "resources/max"));

		$response = new RequestProcessorTest_Response();

		$requestProcessor = new RequestProcessor($setup->getExecutionParameters(), $request, $response);
		$requestProcessor->process();

		$this->assertEquals(1, $response->sent);
		$this->assertEmpty($response->operations);
		$this->assertNull($response->exception);
		$this->assertInstanceOf(DataObject::class, $response->entity);
	}
}

class RequestProcessorTest_Response implements Response
{
	public $operations;
	public $entity;
	public $exception;
	public $value;
	public $sent = 0;

	public function setOperations($operations = array())
	{
		$this->operations = $operations;
	}

	public function setEntity(DataEntity $entity)
	{
		$this->entity = $entity;
	}

	public function setException(\Exception $e)
	{
		$this->exception = $e;
	}

	public function setScalarValue($value)
	{
		$this->value = $value;
	}

	public function send()
	{
		$this->sent++;
	}

}