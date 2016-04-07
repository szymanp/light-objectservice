<?php
namespace Szyman\ObjectService\Request;

use Symfony\Component\HttpFoundation\Request;
use Light\ObjectAccess\Type\Type;
use Light\ObjectAccess\Resource\ResolvedScalar;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\Util\DefaultRelativeAddress;
use Light\ObjectService\TestData\Setup;
use Light\ObjectService\TestData\Post;
use Szyman\ObjectService\Service\ComplexValueRepresentation;
use Szyman\ObjectService\Service\ExecutionEnvironment;
use Szyman\ObjectService\Service\RequestComponents;
use Szyman\ObjectService\Service\RequestType;
use Szyman\ObjectService\Service\RequestResult;
use Szyman\ObjectService\Service\RequestBodyDeserializer;

class StandardRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		
		$this->setup = Setup::create();
	}

	public function testRead()
	{
		$handler = new StandardRequestHandler($this->setup->getExecutionParameters());
		$request = Request::create("http://example.org/");	// Dummy request, not used in handler
		$subject = $this->setup->getEndpointRegistry()->getResource('http://example.org/collections/post');
		$rc = RequestComponents::newBuilder()
			->subjectResource($subject)
			->requestUriResource($subject)
			->requestType(RequestType::get(RequestType::READ))
			->endpointAddress($subject->getAddress())
			->build();

		$result = $handler->handle($request, $rc);
	
		$this->assertInstanceOf(RequestResult::class, $result);
		$this->assertSame($subject, $result->getResource());
	}
	
	public function testCreateComplexViaPOST()
	{
		$handler = new StandardRequestHandler($this->setup->getExecutionParameters());
		$request = Request::create("http://example.org/");	// Dummy request, not used in handler
		$subject = $this->setup->getEndpointRegistry()->getResource('http://example.org/collections/post');
		$rep = $this->getMockBuilder(ComplexValueRepresentation::class)->getMock();
		$rc = RequestComponents::newBuilder()
			->subjectResource($subject)
			->requestType(RequestType::get(RequestType::CREATE))
			->endpointAddress($subject->getAddress())
			->deserializer(new StandardRequestHandlerTest_Deserializer($rep))
			->build();

		$result = $handler->handle($request, $rc);
	
		$this->assertInstanceOf(RequestResult::class, $result);
		$this->assertInstanceOf(ResolvedObject::class, $result->getResource());
		$this->assertInstanceOf(Post::class, $result->getResource()->getValue());
		
		$post = $result->getResource()->getValue();
		$this->assertEquals(5050, $post->getId());
		$this->assertNull($post->getTitle());
		$this->assertNull($post->getText());
	}

	public function testCreateComplexViaPUT()
	{
		$handler = new StandardRequestHandler($this->setup->getExecutionParameters());
		$request = Request::create("http://example.org/");	// Dummy request, not used in handler
		$subject = $this->setup->getEndpointRegistry()->getResource('http://example.org/collections/post');
		$rep = $this->getMockBuilder(ComplexValueRepresentation::class)->getMock();
		$reladdr = new DefaultRelativeAddress($subject);
		$reladdr->appendElement(5123);
		
		$rc = RequestComponents::newBuilder()
			->subjectResource($subject)
			->requestType(RequestType::get(RequestType::CREATE))
			->endpointAddress($subject->getAddress())	// not used in handler
			->relativeAddress($reladdr)
			->deserializer(new StandardRequestHandlerTest_Deserializer($rep))
			->build();

		$result = $handler->handle($request, $rc);
	
		$this->assertInstanceOf(RequestResult::class, $result);
		$this->assertInstanceOf(ResolvedObject::class, $result->getResource());
		$this->assertInstanceOf(Post::class, $result->getResource()->getValue());
		
		$post = $result->getResource()->getValue();
		$this->assertEquals(5123, $post->getId());
		$this->assertNull($post->getTitle());
		$this->assertNull($post->getText());
		$this->assertSame($post, $this->setup->getDatabase()->getPost(5123));
	}

}

class StandardRequestHandlerTest_Deserializer implements RequestBodyDeserializer
{
	private $result;
	
	public function __construct($result)
	{	
		$this->result = $result;
	}

	public function deserialize($content)
	{
		return $this->result;
	}
}
