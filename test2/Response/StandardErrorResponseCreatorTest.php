<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Exception\MalformedRequest;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Resource\Projection\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\ObjectService\Configuration\Util\TypeBasedResponseContentTypeMap;
use Szyman\ObjectService\Service\ExceptionRequestResult;

class StandardErrorResponseCreatorTest extends AbstractResponseCreatorTest
{
	protected function newResponseCreator($structureSer, $dataSer, $map)
	{
		return new StandardErrorResponseCreator($structureSer, $dataSer, $map);
	}

	public function testInternals()
	{
		$request = Request::create("http://www.example.org/some-url");
		$requestResult = new ExceptionRequestResult(new \Exception("Something went wrong?"));
		$response = $this->creator->newResponse($request, $requestResult);

		$this->assertInstanceOf(Response::class, $response);
		$this->assertInstanceOf(DataObject::class, $this->structureSerializer->data);
		$this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
	}

	public function testMalformedRequest()
	{
		$request = Request::create("http://www.example.org/some-url");
		$requestResult = new ExceptionRequestResult(new MalformedRequest("Something went wrong?"));
		$response = $this->creator->newResponse($request, $requestResult);

		$this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
		$this->assertEquals("dummy string", $response->getContent());
		$this->assertEquals('application/vnd.exception+json', $response->headers->get('CONTENT_TYPE'));
	}
}
