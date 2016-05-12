<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Resource\Selection\NestedCollectionSelection;
use Light\ObjectService\Resource\Selection\NestedComplexSelection;
use Light\ObjectService\Resource\Selection\Selection;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\Setup;

class SelectionTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();

		$this->setup = Setup::create();
	}

	public function testNestedSelections()
	{
		$typeRegistry = $this->setup->getTypeRegistry();

		$select = Selection::create($typeRegistry->getTypeHelperByName(Author::class));
		$subselect = $select->subselect("posts");

		$this->assertInstanceOf(NestedCollectionSelection::class, $subselect);
		$subselect->fields("id, text");

		$subsubselect = $subselect->subselect("author");
		$this->assertInstanceOf(NestedComplexSelection::class, $subsubselect);

		$this->assertInstanceOf(NestedCollectionSelection::class, $select->getSubSelection("posts"));
		$this->assertInstanceOf(NestedComplexSelection::class, $subselect->getSubSelection("author"));
	}

	/**
	 * @expectedException 			\Light\ObjectAccess\Exception\TypeException
	 * @expectedExceptionMessage	Property Light\ObjectService\TestData\AuthorType::invalid does not exist
	 */
	public function testSelectInvalidField()
	{
		$typeRegistry = $this->setup->getTypeRegistry();

		$select = Selection::create($typeRegistry->getTypeHelperByName(Author::class));
		$select->fields("id, invalid");
	}

	public function testSelectAllFields()
	{
		$typeRegistry = $this->setup->getTypeRegistry();

		$select = Selection::create($typeRegistry->getTypeHelperByName(Author::class));
		$select->fields("*");

		$this->assertEquals(array("id", "name", "age", "posts"), $select->getFields());
	}

	public function testSelectAllFieldsMinusSome()
	{
		$typeRegistry = $this->setup->getTypeRegistry();

		$select = Selection::create($typeRegistry->getTypeHelperByName(Author::class));
		$select->fields("*, -age");

		$this->assertEquals(array("id", "name", "posts"), $select->getFields());
	}

}
