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
	
	public function testResolution()
	{
		$registry = $this->createEndpointRegistry();
		
		$url = "http://example.org/endpoint/resource";
		$resourceIdentifier = ResourceIdentifier::createFromUrl($url);
		$resolved = $resourceIdentifier->resolve($registry);

		$this->assertEquals("", $resolved->getPath());
		$this->assertEquals(0, count($resolved->getElements()));
		$this->assertEquals("resource", $resolved->getSourceResource()->getEndpointUrl()->getRelativeUrl());
	}
	
	protected function createEndpointRegistry()
	{
		$registry = new EndpointRegistry();
		$endpoint = Endpoint::create("http://example.org/endpoint");
		$endpoint->getObjectRegistry()->publishObject("resource", new Post(), new PostModel());
		$registry->addEndpoint($endpoint);

		return $registry;
	}
}
 