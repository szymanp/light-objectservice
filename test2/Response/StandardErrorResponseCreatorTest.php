<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Exception\MalformedRequest;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Resource\Projection\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\ObjectService\Configuration\Util\TypeBasedResponseContentTypeMap;
use Szyman\ObjectService\Service\ExceptionRequestResult;

class StandardErrorResponseCreatorTest extends \PHPUnit_Framework_TestCase
{
	/** @var StandardErrorResponseCreatorTest_StructureSerializer */
	private $structureSerializer;
	/** @var StandardErrorResponseCreatorTest_DataSerializer */
	private $dataSerializer;
	/** @var TypeBasedResponseContentTypeMap */
	private $map;
	/** @var StandardErrorResponseCreator */
	private $creator;

	protected function setUp()
	{
		$this->structureSerializer = new StandardErrorResponseCreatorTest_StructureSerializer;
		$this->dataSerializer = new StandardErrorResponseCreatorTest_DataSerializer;
		$this->map = new TypeBasedResponseContentTypeMap();
		$this->map->addClass(\Exception::class, "TEST", "application/vnd.exception+json");

		$this->creator = new StandardErrorResponseCreator($this->structureSerializer, $this->dataSerializer, $this->map);
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

class StandardErrorResponseCreatorTest_StructureSerializer implements StructureSerializer
{
	public $data;

	public function serializeStructure(DataEntity $dataEntity)
	{
		$this->data = $dataEntity;
		return "nothing";
	}
}

class StandardErrorResponseCreatorTest_DataSerializer implements DataSerializer
{
	public $data;

	public function serializeData($data)
	{
		$this->data = $data;
		return "dummy string";
	}

	public function getFormatName()
	{
		return "TEST";
	}
}