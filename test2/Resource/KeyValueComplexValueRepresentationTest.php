<?php
namespace Szyman\ObjectService\Resource;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectService\Resource\Addressing\UrlUnresolvedAddress;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;

class KeyValueComplexValueRepresentationTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		parent::setUp();
		$this->setup = Setup::create();
	}

	public function testUpdateScalarFields()
	{
		$rep = new KeyValueComplexValueRepresentation();
		$rep->setValue('id', 123456);
		$rep->setValue('title', 'A test value');

		$post = new Post();
		$resource = $this->newResource($post);
		$rep->updateObject($resource, $this->setup->getExecutionParameters());

		$this->assertEquals(123456, 		$post->getId());
		$this->assertEquals('A test value', $post->getTitle());
	}

	public function testUpdateReferenceFields()
	{
		$rep = new KeyValueComplexValueRepresentation();
		$rep->setResource('author', new ExistingResourceReference(new UrlUnresolvedAddress('http://example.org/resources/max')));

		$post = new Post();
		$resource = $this->newResource($post);
		$rep->updateObject($resource, $this->setup->getExecutionParameters());

		$this->assertSame($this->setup->getDatabase()->getAuthor(1010), $post->getAuthor());
	}

	/**
	 * @expectedException Szyman\ObjectService\Resource\ResourceReferenceException
	 */
	public function testUpdateWithMissingReference()
	{
		$rep = new KeyValueComplexValueRepresentation();
		$rep->setResource('title', new ExistingResourceReference(new UrlUnresolvedAddress('http://example.org/resources/missing')));

		$post = new Post();
		$resource = $this->newResource($post);
		$rep->updateObject($resource, $this->setup->getExecutionParameters());
	}

	/**
	 * @param object $object
	 * @return ResolvedObject
	 */
	private function newResource($object)
	{
		return new ResolvedObject($this->setup->getTypeRegistry()->getComplexTypeHelper(get_class($object)), $object, EmptyResourceAddress::create(), Origin::unavailable());
	}
}
