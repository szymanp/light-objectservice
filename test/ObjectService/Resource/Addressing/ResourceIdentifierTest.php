<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\EndpointRegistry;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\Service\Endpoint;

class ResourceIdentifierTest extends \PHPUnit_Framework_TestCase
{
	public function testCreation()
	{
		$url = "http://example.org/endpoint/resource";
		$resourceIdentifier = ResourceIdentifier::createFromUrl($url);
		$this->assertEquals($url, $resourceIdentifier->getUrl());
	}
	
	public function testResolutionOfRootObject()
	{
		$registry = $this->createEndpointRegistry();

		$url = "http://example.org/endpoint/resource";
		$resourceIdentifier = ResourceIdentifier::createFromUrl($url);
		$resolved = $resourceIdentifier->resolve($registry);

		$resourcePath = $resolved->getResourcePath();

		$this->assertEquals("", $resourcePath->getPath());
		$this->assertEquals(0, count($resourcePath->getElements()));

		$baseResource = $resourcePath->getSourceResource();
		$this->assertEquals("resource", $baseResource->getEndpointUrl()->getRelativeUrl());
		$this->assertEquals("Title", $baseResource->getValue()->title);
		$this->assertInstanceOf('Light\ObjectService\Mockup\PostModel', $baseResource->getType());
	}

	public function testResolutionOfRootObjectProperty()
	{
		$registry = $this->createEndpointRegistry();

		$url = "http://example.org/endpoint/resource/title";
		$resourceIdentifier = ResourceIdentifier::createFromUrl($url);
		$resolved = $resourceIdentifier->resolve($registry);
		$resourcePath = $resolved->getResourcePath();

		$this->assertEquals("title", $resourcePath->getPath());
		$this->assertEquals("title", $resourcePath->getLastElement());
		$this->assertEquals(1, count($resourcePath->getElements()));

		$baseResource = $resourcePath->getSourceResource();
		$this->assertEquals("resource", $baseResource->getEndpointUrl()->getRelativeUrl());
	}

	protected function createEndpointRegistry()
	{
		$registry = new EndpointRegistry();
		$endpoint = Endpoint::create("http://example.org/endpoint");
		$endpoint->getObjectRegistry()->publishObject("resource", new Post(10, "Title"), new PostModel());
		$registry->addEndpoint($endpoint);

		return $registry;
	}
}
 