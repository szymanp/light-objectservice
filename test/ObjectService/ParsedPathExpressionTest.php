<?php

namespace Light\ObjectBroker;

use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Expression\ParsedRootPathExpression;
use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\TypeFactory;
use Light\ObjectService\ObjectRegistry;

require_once 'config.php';
require_once __DIR__ . '/MockupModel.php';

class ParsedPathExpressionTest extends \PHPUnit_Framework_TestCase
{
	private $registry;
	private $model;
	private $commentType;
	
	protected function setUp()
	{
		parent::setUp();

		$this->registry = new ObjectRegistry();
		$this->registry->addType($this->commentType = TypeFactory::getCommentType());
		$this->registry->publishCollection("models/post", $this->model = new PostCollectionModel());
	}
	
	public function testBasicPath()
	{
		$path = new PathExpression();
		$path->setPath("models/post");
		$path->setWhereReference(PathExpression::TARGET, 
			$where = WhereExpression::create($this->model)
			->setValue("id", new Criterion(12)));
		
		$parsed = new ParsedRootPathExpression($path, $this->registry);
		$elements = $parsed->getPathElements();
		
		$this->assertEquals("models/post", $parsed->getRootResourceName());
		$this->assertSame($this->model, $parsed->getRootType());
		$this->assertNull($parsed->getRootObject());
		$this->assertSame($where, $elements[0]);
	}
	
	public function testPathWithWhereRefs()
	{
		$path = new PathExpression();
		$path->setPath("models/post/_1/comments/_2");
		$path->setWhereReference("_1", $where1 = WhereExpression::create($this->model));
		$path->setWhereReference("_2", $where2 = WhereExpression::create($this->commentType));
	
		$parsed = new ParsedRootPathExpression($path, $this->registry);
		$elements = $parsed->getPathElements();
	
		$this->assertEquals("models/post", $parsed->getRootResourceName());
		$this->assertSame($this->model, $parsed->getRootType());
		$this->assertNull($parsed->getRootObject());
		$this->assertSame($where1, $elements[0]);
		$this->assertEquals("comments", $elements[1]);
		$this->assertSame($where2, $elements[2]);
	}

    /**
     * @expectedException        \Light\ObjectService\Exceptions\InvalidRequestException
     * @expectedExceptionMessage WHERE reference "_3" used in path "models/post/_3/comments/_2" is not defined
     */
	public function testPathWithInvalidRef()
	{
		$path = new PathExpression();
		$path->setPath("models/post/_3/comments/_2");
		$path->setWhereReference("_1", $where1 = WhereExpression::create($this->model));
		$path->setWhereReference("_2", $where2 = WhereExpression::create($this->commentType));
	
		$parsed = new ParsedRootPathExpression($path, $this->registry);
	}
}

