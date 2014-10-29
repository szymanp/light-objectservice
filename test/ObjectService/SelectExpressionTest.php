<?php
namespace Light\ObjectService;

use Light\ObjectService\Expression\SelectExpression;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Service\Endpoint;

require_once 'config.php';
require_once __DIR__ . '/MockupModel.php';

class SelectExpressionTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Light\ObjectService\ObjectRegistry */ 
	private $registry;
	private $postModel;
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->registry = new ObjectRegistry(Endpoint::createInternal());
		$this->registry->addType($this->postModel = new PostModel());
		$this->registry->publishCollection("models/post", new PostCollectionModel());
	}
	
	/**
	 * @expectedException        \Light\ObjectService\Exceptions\TypeException
	 * @expectedExceptionMessage Field "missing" does not exist in specification for class "Light\ObjectService\Mockup\Post"
	 */
	public function testMissingProperty()
	{
		$selection = SelectExpression::create($this->postModel)->fields("id, missing");
	}
}

