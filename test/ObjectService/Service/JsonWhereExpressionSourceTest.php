<?php
namespace Light\ObjectBroker\Service;

use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Mockup\CommentCollectionType;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Service\Json\JsonWhereExpressionSource;

require_once 'config.php';
require_once __DIR__ . '/../MockupModel.php';

class JsonWhereExpressionSourceTest extends \PHPUnit_Framework_TestCase
{
	private $postType,
			$postCollectionType,
			$commentCollectionType;
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->registry = new ObjectRegistry();
		$this->registry->addType($this->postType = new PostModel());
		$this->registry->addType($this->postCollectionType = new PostCollectionModel());
		$this->registry->addType($this->commentCollectionType = new CommentCollectionType());
	}
	
	public function testSimpleCriterion()
	{
		$body = <<<DOC
		{
			"id": 5,
			"title": { "like": ["%keyword%", "%name%"] }
		}
DOC;
		$source = JsonWhereExpressionSource::create(json_decode($body));
		$expr = $source->compile($this->postCollectionType);
		
		$criteria = $expr->getValues("id");
		$this->assertEquals(1, count($criteria));
		$this->assertEquals(Criterion::EQ, $criteria[0]->getOperator());
		$this->assertEquals(5, $criteria[0]->getValue());
		
		$criteria = $expr->getValues("title");
		$this->assertEquals(2, count($criteria));
		$this->assertEquals(Criterion::LIKE, $criteria[0]->getOperator());
		$this->assertEquals(Criterion::LIKE, $criteria[1]->getOperator());
		$this->assertEquals("%keyword%", $criteria[0]->getValue());
		$this->assertEquals("%name%", $criteria[1]->getValue());
		
	}
}

