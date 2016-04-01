<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedNull;
use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectAccess\Type\TypeHelper;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;

class RequestComponentsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Tests that the builder works correctly.
	 */
	public function testBuilder()
	{
		$rcb = RequestComponents::newBuilder();
		
		$resource = new ResolvedNull(
					$this->getMockBuilder(TypeHelper::class)
						->disableOriginalConstructor()
						->getMock(),
					EmptyResourceAddress::create(),
					Origin::unavailable());

		$rcb->subjectResource($resource);
		$rcb->endpointAddress($this->getMockBuilder(EndpointRelativeAddress::class)->disableOriginalConstructor()->getMock());
		$rcb->requestHandler($this->getMockBuilder(RequestHandler::class)->getMock());
		$rcb->responseCreator($this->getMockBuilder(ResponseCreator::class)->getMock());
		
		$rc = $rcb->build();
		$this->assertInstanceOf(RequestComponents::class, $rc);
		
		$this->assertSame($resource, $rc->getSubjectResource());
	}
}
