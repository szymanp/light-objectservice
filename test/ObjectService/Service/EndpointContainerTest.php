<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\TestData\RemoteJsonClient;
use Light\ObjectService\TestData\Post;
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
		$client = new RemoteJsonClient();
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
		$client = new RemoteJsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->get("resources/max");

		$this->assertArrayHasKey("links", $result);
		$this->assertArrayHasKey("data", $result);

		$this->assertEquals("Max Ray", $result['data']['name']);
	}

	public function testReadPosts()
	{
		$client = new RemoteJsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->get("collections/post");

		$this->assertArrayHasKey("links", $result);
		$this->assertArrayHasKey("data", $result);

		$this->assertArrayHasKey("4040", $result['data']);
		$this->assertArrayHasKey("4041", $result['data']);
		$this->assertArrayHasKey("4042", $result['data']);
	}

	public function testReadPostWithInclusiveSelection()
	{
		$client = new RemoteJsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->get("collections/post/4040?select=id,title");

		$this->assertArrayHasKey("links", $result);
		$this->assertArrayHasKey("data", $result);

		$this->assertArrayHasKey("id", $result['data']);
		$this->assertArrayHasKey("title", $result['data']);
		$this->assertArrayNotHasKey("author", $result['data']);
	}

	public function testReadPostWithExclusiveSelection()
	{
		$client = new RemoteJsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->get("collections/post/4040?select=*,-author");

		$this->assertArrayHasKey("links", $result);
		$this->assertArrayHasKey("data", $result);

		$this->assertArrayHasKey("id", $result['data']);
		$this->assertArrayHasKey("title", $result['data']);
		$this->assertArrayNotHasKey("author", $result['data']);
	}

	public function testCreatePost()
	{
		$client = new RemoteJsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->post("collections/post", [
			'meta' => [
				'spec' => 'new',
				'type' => "php:" . Post::class
			],
			'data' => [
				'title' => "My newly created post"
			]
		]);

		$this->assertArrayHasKey("links", $result);
		$this->assertArrayHasKey("data", $result);

		$this->assertEquals($client->getServiceUrl() . "/collections/post/5050",
							$client->getLastHeader("Location"));

		$this->assertArrayHasKey("4040", $result['data']);
		$this->assertArrayHasKey("4041", $result['data']);
		$this->assertArrayHasKey("4042", $result['data']);
		// TODO The ID of the new post should come from a HTTP header.
		$this->assertArrayHasKey("5050", $result['data']);

		$newElement = $result['data']['5050'];
		$this->assertEquals("My newly created post", $newElement['data']['title']);
		$this->assertNull($newElement['data']['text']);
		$this->assertNull($newElement['data']['author']);
	}

	public function testUpdatePost()
	{
		$client = new RemoteJsonClient();
		$client->skipTestIfNotConfigured();

		$result = $client->patch("collections/post/4041", [
			'title' => "My updated title",
			'text' => "Ipsum ipsum"
		]);

		$this->assertArrayHasKey("links", $result);
		$this->assertArrayHasKey("data", $result);

		$this->assertArrayHasKey("id", $result['data']);
		$this->assertArrayHasKey("title", $result['data']);
		$this->assertArrayHasKey("text", $result['data']);
		$this->assertArrayHasKey("author", $result['data']);

		$data = $result['data'];
		$this->assertEquals("My updated title", $data['title']);
		$this->assertEquals("Ipsum ipsum", $data['text']);
		$this->assertEquals(1010, $data['author']['data']['id']);
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