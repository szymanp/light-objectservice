<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Resource\Query\Scope;
use Light\ObjectService\Resource\Util\ResourcePathBuilder;
use Light\ObjectService\Service\Endpoint;

class ResourcePathBuilderTest extends \PHPUnit_Framework_TestCase
{
	private $objectRegistry;
	private $post;
	private $postModel;

	protected function setUp()
	{
		parent::setUp();

		$this->objectRegistry = new ObjectRegistry(Endpoint::createInternal());
		$this->objectRegistry->publishObject("models/resource", $this->post = new Post(10, "Title"), $this->postModel = new PostModel());
	}

	public function testObjectFromRegistry()
	{
		$path = ResourcePathBuilder::createFromRegistry($this->objectRegistry)
			->appendPath("models/resource/remainder/123")
			->build();
		$this->assertSame($this->post, $path->getSourceResource()->getValue());
		$this->assertEquals("remainder/123", $path->getPath());
	}

	public function testObjectFromRegistryWithScope()
	{
		$path = ResourcePathBuilder::createFromRegistry($this->objectRegistry)
			->appendPath("models/resource/collection")
			->appendScope(Scope::createEmptyScope())
			->build();
		$this->assertSame($this->post, $path->getSourceResource()->getValue());
		$this->assertNull($path->getPath());
		$this->assertFalse($path->hasPath());

		$elements = $path->getElements();
		$this->assertEquals(2, count($elements));
		$this->assertEquals("collection", $elements[0]);
		$this->assertTrue($elements[1] instanceof Scope);
	}

}
 