<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedScalar;
use Light\ObjectAccess\Type\TypeProvider;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\PostCollectionType;
use Szyman\ObjectService\Configuration\Endpoint;
use Szyman\ObjectService\Configuration\Util\DefaultObjectProvider;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\AuthorType;
use Light\ObjectService\TestData\Database;

class DefaultObjectProviderTest extends \PHPUnit_Framework_TestCase
{
	/** @var TypeProvider */
	private $typeProvider;

	protected function setUp()
	{
		parent::setUp();

		$this->typeProvider = new DefaultTypeProvider();
		$this->typeProvider->addType(new AuthorType(new Database()));
		$this->typeProvider->addType(new PostCollectionType(new Database()));
	}

	public function testPublishValue()
	{
		$objectProvider = new DefaultObjectProvider();
		$endpoint = Endpoint::create("//", $objectProvider, $this->typeProvider);

		$simpleValue = 123;
		$complexValue = new Author(1010, "John Doe");

		$objectProvider->publishValue("resource/simple", $simpleValue);
		$objectProvider->publishValue("resource/complex", $complexValue);

		$factory = $objectProvider->getResourceFactory("resource/simple");
		$resource = $factory->createResource($endpoint);

		$this->assertInstanceOf(ResolvedScalar::class, $resource);
		$this->assertEquals($simpleValue, $resource->getValue());
		$this->assertEquals("integer", $resource->getTypeHelper()->getName());
		$this->assertEquals("//resource/simple", $resource->getAddress()->getAsString());

		$factory = $objectProvider->getResourceFactory("resource/complex");
		$resource = $factory->createResource($endpoint);
		$this->assertInstanceOf(ResolvedObject::class, $resource);
		$this->assertSame($complexValue, $resource->getValue());
		$this->assertEquals(Author::class, $resource->getTypeHelper()->getName());
		$this->assertEquals("//resource/complex", $resource->getAddress()->getAsString());
	}

	public function testPublishCollection()
	{
		$objectProvider = new DefaultObjectProvider();
		$endpoint = Endpoint::create("//", $objectProvider, $this->typeProvider);

		$objectProvider->publishCollection("resource/posts", Post::class . '[]');

		$factory = $objectProvider->getResourceFactory("resource/posts");
		$this->assertNotNull($factory);
		$resource = $factory->createResource($endpoint);

		$this->assertInstanceOf(ResolvedCollectionResource::class, $resource);
	}
}
