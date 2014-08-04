<?php
namespace Light\ObjectBroker\Service;

use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Service\Request\ReadOperation;
use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Service\Util\ExecutionParametersObject;

require_once 'config.php';
require_once __DIR__ . '/../MockupModel.php';

class OperationTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Light\ObjectService\ObjectRegistry */ 
	private $registry;
	/** @var Post */
	private $currentPost;
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->registry = new ObjectRegistry();
		$this->registry->addType($postModel = new PostModel());
		$this->registry->publishCollection("models/post", new PostCollectionModel());
		$this->registry->publishObject("current/post", $this->currentPost = new Post(911, "Current post"));
	}	
	
	public function testReadOperation()
	{
		$path = new PathExpression();
		$path->setPath("models/post/141");
		
		$params = new ExecutionParametersObject();
		$params->setObjectRegistry($this->registry);
		
		$op = new ReadOperation();
		$op->setResourcePath($path);
		$op->execute($params);
		
		$this->assertNotNull($op->getResource());
		$this->assertNotNull($op->getResource()->getValue());
		$this->assertInstanceOf("Light\ObjectService\Mockup\Post", $op->getResource()->getValue());
		$this->assertEquals(141, $op->getResource()->getValue()->id);
	}
}

