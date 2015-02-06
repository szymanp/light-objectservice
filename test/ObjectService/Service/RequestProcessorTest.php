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

		$requestProcessor = new RequestProcessor($setup->getExecutionParameters(), $request);
		$requestProcessor->process();

		$this->assertFalse($requestProcessor->hasException());
		$this->assertTrue($requestProcessor->hasEntity());
		$this->assertInstanceOf(DataObject::class, $requestProcessor->getEntity());
	}
}