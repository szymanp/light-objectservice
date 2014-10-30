<?php
namespace Light\ObjectService;

use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Resource\Query\WhereExpression;
use Light\ObjectService\Mockup\Database;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\Resource\Addressing\ResourcePath;
use Light\ObjectService\Resource\Query\Scope;
use Light\ObjectService\Resource\Util\ResourcePathBuilder;
use Light\ObjectService\Service\Endpoint;
use Light\ObjectService\Type\PathReader;
use Light\ObjectService\Resource\ResolvedValue;

class PathReaderTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Light\ObjectService\ObjectRegistry */ 
	private $registry;
	/** @var Post */
	private $currentPost;
	
	private $postCollectionModel;
	
	protected function setUp()
	{
		parent::setUp();
		
		Database::initialize();
		
		$this->registry = new ObjectRegistry(Endpoint::createInternal());
		$this->registry->addType($postModel = new PostModel());
		$this->registry->publishCollection("models/post", $this->postCollectionModel = new PostCollectionModel());
		$this->registry->publishObject("current/post", $this->currentPost = new Post(911, "Current post"));
	}
	
	public function testFindAll()
	{
		$postModel = $this->registry->getProvider("models/post");

		$path = ResourcePathBuilder::createFromRegistry($this->registry)
				->appendPath("models/post")
				->appendScope(Scope::createEmptyScope())
				->build();

		$result = $this->getReader($path)->read();
		
		$this->assertTrue(is_array($result->getValue()));
		$this->assertEquals(Database::$posts, $result->getValue());
	}
	
	public function testFindByKey()
	{
		$postModel = $this->registry->getProvider("models/post");

		$path = ResourcePathBuilder::createFromRegistry($this->registry)
				->appendPath("models/post/142")
				->build();

		$result = $this->getReader($path)->read();
	
		$this->assertEquals(Database::$posts[1], $result->getValue());
	}
	
	public function testFindNoValue()
	{
		$path = ResourcePathBuilder::createFromRegistry($this->registry)
			->appendPath("models/post")
			->build();
		
		$result = $this->getReader($path)->read();
		$this->assertTrue($result->isCollection());
		$this->assertTrue($result->isUnresolvedCollection());
	}
	
    /**
     * @expectedException        \Light\ObjectService\Exceptions\ResolutionException
     * @expectedExceptionMessage Cannot read a single element of a multi-element collection
     */
	public function testFindAllAndReadField()
	{
		$path = ResourcePathBuilder::createFromRegistry($this->registry)
			->appendPath("models/post")
			->appendScope(Scope::createEmptyScope())
			->appendPath("title")
			->build();

		$result = $this->getReader($path)->read();
	}

	public function testReadArrayProperty()
	{
		$scope = new Scope();
		$scope->setQuery(
			WhereExpression::create($this->postCollectionModel)
			->setValue("id", new Criterion(142)));

		$path = ResourcePathBuilder::createFromRegistry($this->registry)
			->appendPath("models/post")
			->appendScope($scope)
			->appendPath("tags")
			->build();

		$result = $this->getReader($path)->read();
		
		$this->assertEquals(array("post", "story"), $result->getValue());
	}
	
	public function testReadFromBuiltinArray()
	{
		$path = ResourcePathBuilder::createFromRegistry($this->registry)
			->appendPath("models/post/142/tags/0")
			->build();
	
		$result = $this->getReader($path)->read();
	
		$this->assertEquals("post", $result->getValue());
	}
	
	public function testReadPublishedObject()
	{
		$path = ResourcePathBuilder::createFromRegistry($this->registry)
			->appendPath("current/post/title")
			->build();
		
		$result = $this->getReader($path)->read();
		
		$this->assertEquals("Current post", $result->getValue());
	}
	
	public function testNestedPathExpression()
	{
		$path1 = ResourcePathBuilder::createFromRegistry($this->registry)
			->appendPath("current/post")
			->build();

		$result1 = $this->getReader($path1)->read();

		$path2 = ResourcePathBuilder::createFromResource($result1)
			->appendPath("title")
			->build();
		
		$result2 = $this->getReader($path2)->read();
		
		$this->assertSame($this->currentPost, $result1->getValue());
		$this->assertEquals("Current post", $result2->getValue());
	}
	
	/**
	 * @param ResourcePath $path
	 * @return \Light\ObjectService\Type\PathReader
	 */
	private function getReader(ResourcePath $path)
	{
		return new PathReader($path, $this->registry);
	}

}

