<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectAccess\Resource\Addressing\RelativeAddress;
use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\ObjectService\Service\RequestComponents;
use Szyman\ObjectService\Service\RequestType;
use Szyman\ObjectService\Service\ResourceRequestResult;

class StandardResourceResponseCreatorTest extends AbstractResponseCreatorTest
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
	}

	protected function newResponseCreator($structureSer, $dataSer, $map)
	{
		$map->addClass(Post::class, "TEST", "application/vnd.post+json");
		$map->addClass(Author::class, "TEST", "application/vnd.author+json");
		return new StandardResourceResponseCreator($structureSer, $dataSer, $map);
	}

	public function testReadViaGET()
	{
		$endpointRegistry = $this->setup->getEndpointRegistry();

		$resource = $endpointRegistry->getResource('http://example.org/resources/max');

		$requestComponents = RequestComponents::newBuilder()
			->endpointAddress($this->getMockBuilder(EndpointRelativeAddress::class)->disableOriginalConstructor()->getMock())
			->relativeAddress($this->getMockBuilder(RelativeAddress::class)->getMock())
			->requestType(RequestType::get(RequestType::READ))
			->subjectResource($resource)
			->requestUriResource($resource)
			->build();

		$request = Request::create("http://www.example.org/some-url", 'GET');
		$requestResult = new ResourceRequestResult($resource);
		$response = $this->creator->newResponse($request, $requestResult, $requestComponents);

		$this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
		$this->assertEquals('application/vnd.author+json', $response->headers->get('CONTENT_TYPE'));
	}

	public function testCreateComplexViaPUT()
	{
		$endpointRegistry = $this->setup->getEndpointRegistry();

		$resource = new ResolvedObject(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class),
			new Post(),
			EmptyResourceAddress::create(),
			Origin::unavailable());

		$requestComponents = RequestComponents::newBuilder()
			->endpointAddress($this->getMockBuilder(EndpointRelativeAddress::class)->disableOriginalConstructor()->getMock())
			->relativeAddress($this->getMockBuilder(RelativeAddress::class)->getMock())
			->requestType(RequestType::get(RequestType::CREATE))
			->subjectResource($endpointRegistry->getResource('http://example.org/collections/post'))
			->build();

		$request = Request::create("http://www.example.org/some-url", 'PUT');
		$requestResult = new ResourceRequestResult($resource);
		$response = $this->creator->newResponse($request, $requestResult, $requestComponents);

		$this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
		$this->assertEquals("dummy string", $response->getContent());
		$this->assertEquals('application/vnd.post+json', $response->headers->get('CONTENT_TYPE'));
	}

}
