<?php
namespace Szyman\ObjectService\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\ObjectService\Configuration\Util\DefaultResponseContentTypeMap;

class CustomResponseCreatorTest extends \PHPUnit_Framework_TestCase
{
	/** @var StandardResponseCreatorFactory */
	private $factory;

	protected function setUp()
	{
		$this->factory = new StandardResponseCreatorFactory(new DefaultResponseContentTypeMap());
	}

	public function testStandardResponseFactory()
	{
		$request = Request::create("http://example.org/resources/somewhere");
		$request->headers->set('ACCEPT', 'text/plain, application/json');
		$requestResult = CustomRequestResult::newClosure(function()
		{
			return Response::create('hello');
		});

		$result = $this->factory->newResponseCreator($request, $requestResult, null);
		$this->assertInstanceOf(CustomResponseCreator::class, $result);

		$response = $result->newResponse($request, $requestResult, null);
		$this->assertEquals('hello', $response->getContent());
	}
}
