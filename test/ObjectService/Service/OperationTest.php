<?php
namespace Light\ObjectBroker\Service;

use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Mockup\Database;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Resource\Operation\ReadOperation;
use Light\ObjectService\Resource\Operation\UpdateOperation;
use Light\ObjectService\Resource\UrlResourceSpecification;
use Light\ObjectService\Service\Util\ExecutionParametersObject;
use Light\ObjectService\Transaction\Transaction;

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

		Database::initialize();

		$this->registry = new ObjectRegistry();
		$this->registry->addType($postModel = new PostModel());
		$this->registry->publishCollection("models/post", new PostCollectionModel());
		$this->registry->publishObject("current/post", $this->currentPost = new Post(911, "Current post"));
	}	
	
	public function testUpdateOperation()
	{
		$params = new ExecutionParametersObject();
		$params->setObjectRegistry($this->registry);
		$params->setTransaction(new Transaction());

		$resourceSpecification = new UrlResourceSpecification();
		$resourceSpecification->setUrl("//models/post/141");
		$result = $resourceSpecification->execute($params);

		$op = new UpdateOperation();
		$op->setResource($result->getTargetResource());
		$op->setFieldValue("title", "Updated title");
		$op->execute($params);

		$this->assertEquals("Updated title", Database::$posts[0]->title);
	}
}
