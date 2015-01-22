<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectService\TestData\Setup;

class TraverseOperationTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
	}

	public function testTraverse()
	{
		$resource = $this->setup->getEndpointRegistry()->getResource("http://example.org/resources/max");

		$updateOperation = new UpdateOperation();
		$updateOperation->setValue("title", $newTitle = "Updated post!");

		$traverseOperation = new TraverseOperation();
		$traverseOperation->addOperation("posts/4040", $updateOperation);
		$traverseOperation->addOperation("posts/4041", $updateOperation);
		$traverseOperation->execute($resource, $this->setup->getExecutionParameters());

		$this->assertEquals($newTitle, $this->setup->getDatabase()->getPost(4040)->getTitle());
		$this->assertEquals($newTitle, $this->setup->getDatabase()->getPost(4041)->getTitle());
	}
}
