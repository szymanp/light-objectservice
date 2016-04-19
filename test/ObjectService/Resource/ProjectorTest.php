<?php
namespace Light\ObjectService\Resource;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Projection\DataCollection;
use Light\ObjectService\Resource\Projection\DataObject;
use Light\ObjectService\Resource\Projection\Projector;
use Light\ObjectService\Resource\Selection\Selection;
use Light\ObjectService\Resource\Util\DataEntityPrinter;
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

	public function testSingleObject()
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

	public function testNestedObjectsDefaultSelection()
	{
		$author = $this->setup->getDatabase()->getAuthor(1010);
		$authorResource = ResolvedValue::create(
			$this->setup->getTypeRegistry()->getTypeHelperByValue($author),
			$author,
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "authors/max"),
			Origin::unavailable());

		$projector = new Projector();
		$result = $projector->project($authorResource);
		$this->assertInstanceOf(DataObject::class, $result);
		$this->assertEquals("http://example.org/authors/max", $result->getResourceAddress()->getAsString());
		$this->assertEquals($author->getId(), $result->getData()->id);
		$this->assertEquals($author->getName(), $result->getData()->name);
		$this->assertEquals($author->getAge(), $result->getData()->age);

		$postsResult = $result->getData()->posts;
		$this->assertInstanceOf(DataCollection::class, $postsResult);
		$this->assertEquals("http://example.org/authors/max/posts", $postsResult->getResourceAddress()->getAsString());

		$elements = $postsResult->getData();
		$this->assertTrue(is_array($elements));
		$this->assertEquals(2, count($elements));

		$expected = <<<EOT
Light\ObjectService\TestData\Author @ http://example.org/authors/max {
  id: 1010
  name: Max Ray
  age: 35
  posts: Light\ObjectService\TestData\Post[] @ http://example.org/authors/max/posts [
    0: Light\ObjectService\TestData\Post @ http://example.org/collections/post/4040 {
      id: 4040
      title: First post
      text: Lorem ipsum dolor
      author: Light\ObjectService\TestData\Author @ http://example.org/authors/max/posts/0/author {}
    }
    1: Light\ObjectService\TestData\Post @ http://example.org/collections/post/4041 {
      id: 4041
      title: Second post
      text: Lorem lorem
      author: Light\ObjectService\TestData\Author @ http://example.org/authors/max/posts/1/author {}
    }
  ]
}
EOT;
		$this->assertEquals(str_replace("\r", "", $expected), DataEntityPrinter::getPrintout($result));
	}
}
