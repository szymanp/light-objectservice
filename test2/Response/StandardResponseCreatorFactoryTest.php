<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectAccess\Resource\ResolvedResource;
use Symfony\Component\HttpFoundation\Request;
use Szyman\ObjectService\Configuration\Util\DefaultResponseContentTypeMap;
use Szyman\ObjectService\Service\ExceptionRequestResult;
use Szyman\ObjectService\Service\ResourceRequestResult;

class StandardResponseCreatorFactoryTest extends \PHPUnit_Framework_TestCase
{
	/** @var StandardResponseCreatorFactory */
	private $factory;

	protected function setUp()
	{
		$this->factory = new StandardResponseCreatorFactory(new DefaultResponseContentTypeMap());
	}

	public function testResourceWithApplicationJson()
	{
		$request = Request::create("http://example.org/resources/somewhere");
		$request->headers->set('ACCEPT', 'text/plain, application/json');
		$requestResult = new ResourceRequestResult($this->getMockBuilder(ResolvedResource::class)->disableOriginalConstructor()->getMock());

		$result = $this->factory->newResponseCreator($request, $requestResult, null);
		$this->assertInstanceOf(StandardResourceResponseCreator::class, $result);
	}

	public function testExceptionWithApplicationJson()
	{
		$request = Request::create("http://example.org/resources/somewhere");
		$request->headers->set('ACCEPT', 'text/plain, application/json');
		$requestResult = new ExceptionRequestResult(new \Exception());

		$result = $this->factory->newResponseCreator($request, $requestResult, null);
		$this->assertInstanceOf(StandardErrorResponseCreator::class, $result);
	}
}
