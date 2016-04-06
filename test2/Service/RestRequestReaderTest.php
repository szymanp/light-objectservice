<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Type\Type;
use Light\ObjectService\TestData\Database;
use Light\ObjectService\TestData\Setup;
use Symfony\Component\HttpFoundation\Request;
use Szyman\Exception\NotImplementedException;
use Szyman\ObjectService\Configuration\Configuration;
use Szyman\ObjectService\Configuration\Util\DefaultConfiguration;
use Szyman\ObjectService\Configuration\Util\DefaultRequestBodyTypeMap;
use Szyman\ObjectService\Configuration\Util\PluggableRequestBodyDeserializerFactory;

class RestRequestReaderTest extends \PHPUnit_Framework_TestCase
{
	/** @var Configuration */
	private $conf;
	
	/** @var PluggableRequestBodyDeserializerFactory */
	private $dszFactory;

	/** @var Database */
	private $database;

	protected function setUp()
	{
		parent::setUp();

		$setup = Setup::create();
		$this->database = $setup->getDatabase();
		$typeMap = new DefaultRequestBodyTypeMap();
		$this->dszFactory = new PluggableRequestBodyDeserializerFactory();

		$this->conf = DefaultConfiguration::newBuilder()
			->endpointRegistry($setup->getEndpointRegistry())
			->requestBodyDeserializerFactory($this->dszFactory)
			->requestBodyTypeMap($typeMap)
			->requestHandlerFactory(new RestRequestReaderTest_RequestHandlerFactory)
			->responseCreatorFactory(new RestRequestReaderTest_ResponseCreatorFactory)
			->build();
	}

	/**
	 * Test a successful GET request.
	 */
	public function testGetRequest()
	{
		$reader = new RestRequestReader($this->conf);
		$request = Request::create("http://example.org/resources/max");

		$result = $reader->readRequest($request);
		$this->assertInstanceOf(RequestComponents::class, $result);

		$this->assertNull($result->getDeserializer());
		$this->assertEquals(RequestType::get(RequestType::READ), $result->getRequestType());
		$this->assertEquals("http://example.org/resources/max", $result->getEndpointAddress()->getAsString());
		$this->assertSame($result->getRequestUriResource(), $result->getSubjectResource());
		$this->assertSame($this->database->getAuthor(1010), $result->getSubjectResource()->getValue());
	}

	public function testPutReplaceRequest()
	{
		// Configure a deserializer
		$mockDeserializer = $this->getMockBuilder(RequestBodyDeserializer::class)->getMock();
		
		$this->dszFactory->registerDeserializer(
			RequestBodyDeserializerType::get(RequestBodyDeserializerType::COMPLEX_VALUE_REPRESENTATION),
			'application/json',
			function(Type $type) use ($mockDeserializer)
			{
				return $mockDeserializer;
			});

		$reader = new RestRequestReader($this->conf);
		$request = Request::create("http://example.org/collections/post/4040", 'PUT', [], [], [], ['CONTENT_TYPE' => 'application/json']);

		$result = $reader->readRequest($request);
		$this->assertInstanceOf(RequestComponents::class, $result);

		$this->assertSame($mockDeserializer, $result->getDeserializer());
		$this->assertEquals(RequestType::get(RequestType::REPLACE), $result->getRequestType());
		$this->assertEquals("http://example.org/collections/post/4040", $result->getEndpointAddress()->getAsString());
		$this->assertSame($this->database->getPost(4040), $result->getRequestUriResource()->getValue());
		// Note that we cannot do "same" comparisons as the objects are different instances.
		$this->assertEquals($this->conf->getEndpointRegistry()->getResource("http://example.org/collections/post"), $result->getSubjectResource());
	}

	/**
	 * @expectedException		 Light\ObjectService\Exception\NotFound
	 * @expectedExceptionMessage No endpoint matching this address was found
	 */
	public function testEndpointNotFound()
	{
		$reader = new RestRequestReader($this->conf);
		$request = Request::create("http://example-bad.org/resources/john");
		$reader->readRequest($request);
	}

	/**
	 * @expectedException		 Light\ObjectService\Exception\NotFound
	 * @expectedExceptionMessage No matching resource found
	 */
	public function testPublishedResourceNotFound()
	{
		$reader = new RestRequestReader($this->conf);
		$request = Request::create("http://example.org/resources/john");
		$reader->readRequest($request);
	}

	/**
	 * @expectedException		 Light\ObjectService\Exception\NotFound
	 * @expectedExceptionMessage Target resource not found
	 */
	public function testResourceInCollectionNotFound()
	{
		$reader = new RestRequestReader($this->conf);
		$request = Request::create("http://example.org/collections/post/4090");
		$reader->readRequest($request);
	}
}

// Dummy class, not used by RestRequestReader.
class RestRequestReaderTest_RequestHandlerFactory implements RequestHandlerFactory
{
	public function newRequestHandler(RequestType $requestType)
	{
		throw new NotImplementedException;
	}

}

// Dummy class, not used by RestRequestReader.
class RestRequestReaderTest_ResponseCreatorFactory implements ResponseCreatorFactory
{
	public function newResponseCreator(Request $request, RequestType $requestType, Type $subjectResourceType)
	{
		throw new NotImplementedException;
	}

}
