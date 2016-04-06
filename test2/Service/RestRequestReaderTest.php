<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Type\Type;
use Light\ObjectAccess\Resource\ResolvedScalar;
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
	 * Tests a successful read request
	 * GET http://example.org/resources/max
	 */
	public function testReadComplexValueViaGET()
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

	/**
	 * Replaces a complex object via PUT
	 * PUT http://example.org/collections/post/4040
	 */
	public function testReplaceComplexValueViaPUT()
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
		// We replace an existing object, therefore the resource at uri is the post to be replaced.
		$this->assertSame($this->database->getPost(4040), $result->getRequestUriResource()->getValue());
		// Note that we cannot do "same" comparisons as the objects are different instances.
		$this->assertEquals($this->conf->getEndpointRegistry()->getResource("http://example.org/collections/post"), $result->getSubjectResource());
	}

	/**
	 * Creates a complex object via PUT
	 * PUT http://example.org/collections/post/4049
	 */
	public function testCreateComplexValueViaPUT()
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
		$request = Request::create("http://example.org/collections/post/4049", 'PUT', [], [], [], ['CONTENT_TYPE' => 'application/json']);

		$result = $reader->readRequest($request);
		$this->assertInstanceOf(RequestComponents::class, $result);

		$this->assertSame($mockDeserializer, $result->getDeserializer());
		$this->assertEquals(RequestType::get(RequestType::CREATE), $result->getRequestType());
		$this->assertEquals("http://example.org/collections/post/4049", $result->getEndpointAddress()->getAsString());
		// We create a new object, therefore the resource at uri does not exist.
		$this->assertNull($result->getRequestUriResource());
		// Note that we cannot do "same" comparisons as the objects are different instances.
		$this->assertEquals($this->conf->getEndpointRegistry()->getResource("http://example.org/collections/post"), $result->getSubjectResource());
	}

	/**
	 * Replaces a simple value via PUT
	 * PUT http://example.org/collections/post/4040/title
	 */
	public function testReplaceSimpleValueViaPUT()
	{
		// Configure a deserializer
		$mockDeserializer = $this->getMockBuilder(RequestBodyDeserializer::class)->getMock();
		
		$this->dszFactory->registerDeserializer(
			RequestBodyDeserializerType::get(RequestBodyDeserializerType::SIMPLE_VALUE_REPRESENTATION),
			'text/plain',
			function(Type $type) use ($mockDeserializer)
			{
				return $mockDeserializer;
			});

		$reader = new RestRequestReader($this->conf);
		$request = Request::create("http://example.org/collections/post/4040/title", 'PUT', [], [], [], ['CONTENT_TYPE' => 'text/plain']);

		$result = $reader->readRequest($request);
		$this->assertInstanceOf(RequestComponents::class, $result);

		$this->assertSame($mockDeserializer, $result->getDeserializer());
		$this->assertEquals(RequestType::get(RequestType::REPLACE), $result->getRequestType());
		$this->assertEquals("http://example.org/collections/post/4040/title", $result->getEndpointAddress()->getAsString());
		// We replace an existing value, therefore the resource at uri is the simple value to be replaced.
		$this->assertInstanceOf(ResolvedScalar::class, $result->getRequestUriResource());
		// Note that we cannot do "same" comparisons as the objects are different instances.
		$this->assertSame($this->database->getPost(4040), $result->getSubjectResource()->getValue());
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
