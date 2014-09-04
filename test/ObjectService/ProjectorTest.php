<?php
namespace Light\ObjectBroker;

use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Expression\SelectExpression;
use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Mockup\CommentCollectionType;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Service\Response\DataCollection;
use Light\ObjectService\Service\Response\DataObject;
use Light\ObjectService\Service\Response\Projector;

require_once 'config.php';
require_once __DIR__ . '/MockupModel.php';

class ProjectorTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Light\ObjectService\ObjectRegistry */ 
	private $registry;
	private $postModel;
	private $commentCollectionType;
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->registry = new ObjectRegistry();
		$this->registry->addType($this->postModel = new PostModel());
		$this->registry->addType($this->commentCollectionType = new CommentCollectionType());
		$this->registry->publishCollection("models/post", new PostCollectionModel());
	}
	
	public function testScalarProperties()
	{
		$selection = SelectExpression::create($this->postModel)->fields("id, title");
		$projector = Projector::create($this->registry, $this->postModel);
		
		$post = new Post(123, "This is a title");
		$result = $projector->project($post, $selection);
		
		$this->assertInstanceOf(DataObject::CLASSNAME, $result);
		$this->assertSame($result->getType(), $this->postModel);
		$this->assertEquals($result->getData()->id, 123);
		$this->assertEquals($result->getData()->title, "This is a title");
	}
	
	public function testCollectionOfScalarsProperty()
	{
		$selection = SelectExpression::create($this->postModel)->fields("id, title, tags");
		$projector = Projector::create($this->registry, $this->postModel);
		
		$post = new Post(123, "This is a title");
		$post->tags = array("interesting", "favorite");
		$result = $projector->project($post, $selection);

		$tags = $result->getData()->tags;
		
		$this->assertSame($tags->getType(), $this->registry->getCollectionType("string"));
		$this->assertEquals($post->tags, $tags->getData());
	}
	
	public function testCollectionOfObjectsProperty()
	{
		$selection = SelectExpression::create($this->postModel)->fields("id, title, comments");
		$projector = Projector::create($this->registry, $this->postModel);
		
		$post = $this->postModel->models[0];
		
		$result = $projector->project($post, $selection);
		$this->assertSame($result->getType(), $this->postModel);
		
		$comments = $result->getData()->comments;
		$this->assertInstanceOf(DataCollection::CLASSNAME, $comments);
		$this->assertSame($this->commentCollectionType, $comments->getType());
		$this->assertEquals(2, count($comments->getData()));
		
		$collection = $comments->getData();
		$this->assertSame($this->commentCollectionType->getBaseType(), $collection[0]->getType());
		$this->assertSame($this->commentCollectionType->getBaseType(), $collection[1]->getType());
		
		$this->assertEquals(101, $collection[0]->getData()->id);
		$this->assertEquals(102, $collection[1]->getData()->id);
		$this->assertEquals("John Doe",  $collection[0]->getData()->author);
		$this->assertEquals("Mary Jane", $collection[1]->getData()->author);
	}
	
	public function testCollectionOfObjectsPropertyWithFilter()
	{
		$selection = SelectExpression::create($this->postModel)
					 ->fields("id, title, comments")
					 ->subselect("comments")
					 ->where(WhereExpression::create($this->commentCollectionType)
					 		 ->setValue("id", new Criterion(102)))
					 ->fields("id")
					 ->done();
		$projector = Projector::create($this->registry, $this->postModel);
	
		$post = $this->postModel->models[0];
	
		$result = $projector->project($post, $selection);
		$this->assertSame($result->getType(), $this->postModel);
	
		$comments = $result->getData()->comments;
		$this->assertInstanceOf(DataCollection::CLASSNAME, $comments);
		$this->assertSame($this->commentCollectionType, $comments->getType());
		$this->assertEquals(1, count($comments->getData()));
	
		$collection = $comments->getData();
	
		$this->assertEquals(102, $collection[0]->getData()->id);
		$this->assertFalse(isset($collection[0]->getData()->author));
	}
	
}

