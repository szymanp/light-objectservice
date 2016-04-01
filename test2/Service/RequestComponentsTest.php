<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedNull;
use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectAccess\Type\TypeHelper;

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
		
		$rc = $rcb->build();
		$this->assertInstanceOf(RequestComponents::class, $rc);
		
		$this->assertSame($resource, $rc->getSubjectResource());
	}
}
