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

	/** @var Database */
	private $database;

	protected function setUp()
	{
		parent::setUp();

		$setup = Setup::create();
		$this->database = $setup->getDatabase();
		$typeMap = new DefaultRequestBodyTypeMap();
		$dszFactory = new PluggableRequestBodyDeserializerFactory();

		$this->conf = DefaultConfiguration::newBuilder()
			->endpointRegistry($setup->getEndpointRegistry())
			->requestBodyDeserializerFactory($dszFactory)
			->requestBodyTypeMap($typeMap)
			->requestHandlerFactory(new RestRequestReaderTest_RequestHandlerFactory)
			->responseCreatorFactory(new RestRequestReaderTest_ResponseCreatorFactory)
			->build();
	}

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