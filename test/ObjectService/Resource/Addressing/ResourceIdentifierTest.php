<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\EndpointRegistry;
use Light\ObjectService\Service\Endpoint;

class ResourceIdentifierTest extends \PHPUnit_Framework_TestCase
{
	public function testCreation()
	{
		$url = "http://example.org/endpoint/resource";
		$resourceIdentifier = ResourceIdentifier::create($url);
		$this->assertEquals($url, $resourceIdentifier->getUrl());
	}
	
	public function testResolution()
	{
		$registry = $this->createEndpointRegistry();
		
		$url = "http://example.org/endpoint/resource?query=zzz#hash";
		$resourceIdentifier = ResourceIdentifier::create($url);
		$resolved = $resourceIdentifier->resolve($registry);

		$url = "http://example.org/endpoint/resource?query=zzz";
		$resourceIdentifier = ResourceIdentifier::create($url);
		$resolved = $resourceIdentifier->resolve($registry);

		$url = "http://example.org/endpoint/resource#hash";
		$resourceIdentifier = ResourceIdentifier::create($url);
		$resolved = $resourceIdentifier->resolve($registry);

		$url = "http://example.org/endpoint/resource";
		$resourceIdentifier = ResourceIdentifier::create($url);
		$resolved = $resourceIdentifier->resolve($registry);
	}
	
	protected function createEndpointRegistry()
	{
		$registry = new EndpointRegistry();
		$endpoint = Endpoint::create("http://example.org/endpoint");
		$registry->addEndpoint($endpoint);
		
		return $registry;
	}
}
 