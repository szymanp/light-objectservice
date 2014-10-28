<?php

namespace Light\ObjectBroker;

use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Expression\ParsedNestedPathExpression;
use Light\ObjectService\Expression\ParsedRootPathExpression;
use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Mockup\Database;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Type\PathReader;
use Light\ObjectService\Resource\ResolvedValue;

require_once 'config.php';
require_once __DIR__ . '/MockupModel.php';

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
		
		$this->registry = new ObjectRegistry();
		$this->registry->addType($postModel = new PostModel());
		$this->registry->publishCollection("models/post", $this->postCollectionModel = new PostCollectionModel());
		$this->registry->publishObject("current/post", $this->currentPost = new Post(911, "Current post"));
	}
	
	public function testFindAll()
	{
		$postModel = $this->registry->getProvider("models/post");
		
		$path = new PathExpression();
		$path->setPath("models/post");
		$path->setWhereReference(PathExpression::TARGET, WhereExpression::create($this->postCollectionModel));
		
		$result = $this->getReader($path)->read();
		
		$this->assertTrue(is_array($result->getValue()));
		$this->assertEquals(Database::$posts, $result->getValue());
	}
	
	public function testFindByKey()
	{
		$postModel = $this->registry->getProvider("models/post");
	
		$path = new PathExpression();
		$path->setPath("models/post/142");
	
		$result = $this->getReader($path)->read();
	
		$this->assertEquals(Database::$posts[1], $result->getValue());
	}
	
    /**
     * @expectedException        \Light\ObjectService\Exceptions\ResolutionException
     * @expectedExceptionMessage Resolution of path "models/post" did not produce any value
     */
	public function testFindNoValue()
	{
		$path = new PathExpression();
		$path->setPath("models/post");
		
		$result = $this->getReader($path)->read();
	}
	
    /**
     * @expectedException        \Light\ObjectService\Exceptions\ResolutionException
     * @expectedExceptionMessage Cannot read a single element of a multi-element collection
     */
	public function testFindAllAndReadField()
	{
		$path = new PathExpression();
		$path->setPath("models/post/_1/title");
		$path->setWhereReference("_1", WhereExpression::create($this->postCollectionModel));
	
		$result = $this->getReader($path)->read();
	}

	public function testReadArrayProperty()
	{
		$path = new PathExpression();
		$path->setPath("models/post/_1/tags");
		$path->setWhereReference("_1",
				WhereExpression::create($this->postCollectionModel)
				->setValue("id", new Criterion(142)));
	
		$result = $this->getReader($path)->read();
		
		$this->assertEquals(array("post", "story"), $result->getValue());
	}
	
	public function testReadFromBuiltinArray()
	{
		$path = new PathExpression();
		$path->setPath("models/post/142/tags/0");
	
		$result = $this->getReader($path)->read();
	
		$this->assertEquals("post", $result->getValue());
	}
	
	public function testReadPublishedObject()
	{
		$path = new PathExpression();
		$path->setPath("current/post/title");
		
		$result = $this->getReader($path)->read();
		
		$this->assertEquals("Current post", $result->getValue());
	}
	
	public function testNestedPathExpression()
	{
		$path1 = new PathExpression();
		$path1->setPath("current/post");
		
		$path2 = new PathExpression();
		$path2->setPath("title");
		$path2->setRelativeTo($path1);
		
		$result1 = $this->getReader($path1)->read();
		$result2 = $this->getNestedReader($path2, $result1)->read();
		
		$this->assertSame($this->currentPost, $result1->getValue());
		$this->assertEquals("Current post", $result2->getValue());
	}
	
	/**
	 * @param PathExpression $path
	 * @return \Light\ObjectService\Type\PathReader
	 */
	private function getReader(PathExpression $path)
	{
		$parsed = new ParsedRootPathExpression($path, $this->registry);
		return new PathReader($parsed, $this->registry);
	}
	
	/**
	 * @return \Light\ObjectService\Type\PathReader
	 */
	private function getNestedReader(PathExpression $path, ResolvedValue $value)
	{
		$parsed = new ParsedNestedPathExpression($path, $value);
		return new PathReader($parsed, $this->registry);
	}
}

