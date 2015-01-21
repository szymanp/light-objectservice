<?php
namespace Light\ObjectService\Resource;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Projection\DataObject;
use Light\ObjectService\Resource\Projection\Projector;
use Light\ObjectService\Resource\Selection\Selection;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\Setup;

class ProjectorTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
	}

	public function testSelectFieldsFromSingleObject()
	{
		$author = new Author(1010, "John Doe");
		$author->setAge(45);

		$authorResource = ResolvedValue::create($this->setup->getTypeRegistry()->getTypeHelperByValue($author),
												$author,
												EndpointRelativeAddress::create($this->setup->getEndpoint(), "authors/john"),
												Origin::unavailable());

		$projector = new Projector();

		$result = $projector->project($authorResource, Selection::create($authorResource->getTypeHelper())->fields("id, name, age"));
		$this->assertInstanceOf(DataObject::class, $result);
		$this->assertSame($authorResource->getTypeHelper(), $result->getTypeHelper());
		$this->assertEquals("http://example.org/authors/john", $result->getResourceAddress()->getAsString());
		$this->assertEquals($author->getId(), $result->getData()->id);
		$this->assertEquals($author->getName(), $result->getData()->name);
		$this->assertEquals($author->getAge(), $result->getData()->age);
	}
}
