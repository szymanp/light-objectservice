<?php

namespace Light\ObjectService;

use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\Type\BuiltinCollectionType;
use Light\ObjectService\Type\BuiltinType;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Util\DefaultNameRegistry;

class NameRegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaultSimpleTypeUri()
	{
		$type = new BuiltinType("string");
		$nameRegistry = new DefaultNameRegistry();
		$this->assertEquals("//string#simple", $nameRegistry->getTypeUri($type));
	}

	public function testDefaultComplexTypeUri()
	{
		$type = new ComplexType();
		$type->getSpecification()->classname(PostModel::CLASSNAME);
		$nameRegistry = new DefaultNameRegistry();
		$this->assertEquals("//Light/ObjectService/Mockup/PostModel#complex", $nameRegistry->getTypeUri($type));
	}

	public function testDefaultCollectionTypeUri()
	{
		$type = new ComplexType();
		$type->getSpecification()->classname(PostModel::CLASSNAME);
		$type = new BuiltinCollectionType($type);
		
		$nameRegistry = new DefaultNameRegistry();
		$this->assertEquals("//Light/ObjectService/Mockup/PostModel#collection", $nameRegistry->getTypeUri($type));
	}

	public function testCustomComplexTypeUri()
	{
		$type = new ComplexType();
		$type->getSpecification()->classname(PostModel::CLASSNAME);
		$nameRegistry = new DefaultNameRegistry();
		$nameRegistry->setTypeUri($type, "http://example.org/ns/post");
		$this->assertEquals("http://example.org/ns/post", $nameRegistry->getTypeUri($type));
	}
	
	public function testPrefixTypeUris()
	{
		$nameRegistry = new DefaultNameRegistry();
		$nameRegistry->addTypeBaseUri("", "http://example.org/ns");
		$nameRegistry->addTypeBaseUri("Light\\ObjectService\\Mockup", "http://example.org/ns/mockup");

		$type = new BuiltinType("string");
		$this->assertEquals("http://example.org/ns/string#simple", $nameRegistry->getTypeUri($type));
		
		$type = new ComplexType();
		$type->getSpecification()->classname(PostModel::CLASSNAME);
		$this->assertEquals("http://example.org/ns/mockup/PostModel#complex", $nameRegistry->getTypeUri($type));
	}
	
}

