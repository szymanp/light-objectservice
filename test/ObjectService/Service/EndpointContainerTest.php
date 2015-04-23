<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\TestData\JsonClient;
use Light\ObjectService\TestData\Setup;
use Light\ObjectService\Exception\NotFound;
use Symfony\Component\HttpFoundation\Response;

class EndpointContainerTest extends \PHPUnit_Framework_TestCase
{
	/** @var EndpointContainerTest_HttpResponse */
	private $errorResponse;
	/** @var Setup */
	private $setup;
	/** @var EndpointContainer */
	private $container;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
		$this->container = new EndpointContainer($this->setup->getEndpointRegistry());

		$this->errorResponse = null;
		$this->container->setHttpResponseFactory($this->getResponseFactoryClosure());
	}

	public function testMissingSetup()
	{
		$this->container->run();

		$this->assertNotNull($this->errorResponse);
		$this->assertEquals(1, $this->errorResponse->sent);
		$this->assertEquals(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $this->errorResponse->getStatusCode());
		$this->assertStringMatchesFormat("%aThe container is not configured to handle requests of this type%a", $this->errorResponse->getContent());
	}

	public function testRemoteNotFound()
	{
		$client = new JsonClient();
		$client->skipTestIfNotConfigured();

		try
		{
			$client->get("/not/found");
		}
		catch (\Pest_NotFound $e)
		{
			$result = json_decode($e->getMessage());
			$this->assertEquals(NotFound::class, $result->exceptionClass);

			return;
		}

		$this->fail("Expected an exception");
	}

	public function testReadAuthor()
	{
		$client = new JsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->get("resources/max");

		$this->assertArrayHasKey("links", $result);
		$this->assertArrayHasKey("data", $result);

		$this->assertEquals("Max Ray", $result['data']['name']);
	}

	public function testReadPosts()
	{
		$client = new JsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->get("collections/post");

		$this->assertArrayHasKey("links", $result);
		$this->assertArrayHasKey("data", $result);

		$this->assertArrayHasKey("4040", $result['data']);
		$this->assertArrayHasKey("4041", $result['data']);
		$this->assertArrayHasKey("4042", $result['data']);
	}

	/**
	 * @expectedException \Pest_ClientError
	 */
	public function testCreatePost()
	{
		$client = new JsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->post("collections/post", [
			'data' => [
				'title' => "My newly created post"
			]
		]);
	}

	protected function getResponseFactoryClosure()
	{
		return function($content, $code, $headers = array())
		{
			return $this->errorResponse = new EndpointContainerTest_HttpResponse($content, $code, $headers);
		};
	}
}

class EndpointContainerTest_HttpResponse extends Response
{
	public $sent = 0;

	public function send()
	{
		$this->sent++;
	}
}