<?php
namespace Light\ObjectService\Json\Request;

use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;

class JsonSelectionProxyTest extends \PHPUnit_Framework_TestCase
{
	public function testSelectionFromString()
	{
		$setup = Setup::create();

		$proxy = new JsonSelectionProxy("id, name");
		$proxy->prepare($helper = $setup->getTypeRegistry()->getTypeHelperByName(Author::class));

		$this->assertEquals(array("id", "name"), $proxy->getFields());
		$this->assertSame($helper, $proxy->getTypeHelper());

		// It should be possible to call prepare() twice with the same helper.
		$proxy->prepare($helper);
	}

	/**
	 * @expectedException			\Light\Exception\Exception
	 * @expectedExceptionMessage	The selection proxy was already prepared with a different type
	 */
	public function testPrepareTwoTimes()
	{
		$setup = Setup::create();

		$proxy = new JsonSelectionProxy("id, name");
		$proxy->prepare($setup->getTypeRegistry()->getTypeHelperByName(Author::class));
		$proxy->prepare($setup->getTypeRegistry()->getTypeHelperByName(Post::class));
	}
}
