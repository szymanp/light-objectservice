<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\TestData\Setup;
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