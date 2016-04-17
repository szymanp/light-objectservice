<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectService\TestData\DummyTransactionFactory;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\ObjectService\Configuration\Util\DefaultConfiguration;
use Szyman\ObjectService\Configuration\Util\DefaultRequestBodyTypeMap;
use Szyman\ObjectService\Configuration\Util\TypeBasedResponseContentTypeMap;
use Szyman\ObjectService\Request\StandardRequestBodyDeserializerFactory;
use Szyman\ObjectService\Request\StandardRequestHandlerFactory;
use Szyman\ObjectService\Response\StandardResponseCreatorFactory;

class RequestProcessorTest extends \PHPUnit_Framework_TestCase
{
	/** @var RequestProcessor */
	private $requestProcessor;

	protected function setUp()
	{
		$setup = Setup::create();
		$requestBodyTypeMap = new DefaultRequestBodyTypeMap();
		$contentTypeMap = new TypeBasedResponseContentTypeMap();
		$contentTypeMap->addClass(Post::class, 'JSON', 'application/vnd.post+json');
		$contentTypeMap->addClass(Author::class, 'JSON', 'application/vnd.author+json');
		$contentTypeMap->addClass(\Exception::class, 'JSON', 'application/vnd.exception+json');

		$conf = DefaultConfiguration::newBuilder()
			->endpointRegistry($setup->getEndpointRegistry())
			->requestBodyDeserializerFactory(StandardRequestBodyDeserializerFactory::getInstance())
			->requestBodyTypeMap($requestBodyTypeMap)
			->requestHandlerFactory(StandardRequestHandlerFactory::getInstance())
			->responseCreatorFactory(new StandardResponseCreatorFactory($contentTypeMap))
			->transactionFactory(new DummyTransactionFactory())
			->build();

		$this->requestProcessor = new RequestProcessor($conf);
	}

	/**
	 * @expectedException Light\ObjectService\Exception\UnsupportedMediaType
	 */
	public function testUnsupportedMediaTypeOnGET()
	{
		$request = Request::create('http://example.org/resources/max');
		$response = $this->requestProcessor->handle($request);
	}

	public function testNotFoundOnGET()
	{
		$request = Request::create('http://example.org/resources/not-exists', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
		$response = $this->requestProcessor->handle($request);

		$this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
		$this->assertEquals('application/vnd.exception+json', $response->headers->get('Content-type'));
	}

	public function testReadPostViaGET()
	{
		$request = Request::create('http://example.org/resources/max', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
		$response = $this->requestProcessor->handle($request);

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
		$this->assertEquals('application/vnd.author+json', $response->headers->get('Content-type'));
		$json = json_decode($response->getContent());
		$this->assertTrue(is_object($json));
		$this->assertTrue(isset($json->_links));
	}
}
