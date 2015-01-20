<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedScalar;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Service\Util\DefaultObjectProvider;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\AuthorType;

class DefaultObjectProviderTest extends \PHPUnit_Framework_TestCase
{
	/** @var TypeRegistry */
	private $typeRegistry;

	protected function setUp()
	{
		parent::setUp();

		$typeProvider = new DefaultTypeProvider();
		$typeProvider->addType(new AuthorType());
		$this->typeRegistry = new TypeRegistry($typeProvider);
	}

	public function testPublishValue()
	{
		$objectProvider = new DefaultObjectProvider($this->typeRegistry);
		$endpoint = Endpoint::create("//", $objectProvider);
		$objectProvider->setEndpoint($endpoint);

		$simpleValue = 123;
		$complexValue = new Author(1010, "John Doe");

		$objectProvider->publishValue("resource/simple", $simpleValue);
		$objectProvider->publishValue("resource/complex", $complexValue);

		$resource = $objectProvider->getResource("resource/simple");
		$this->assertInstanceOf(ResolvedScalar::class, $resource);
		$this->assertEquals($simpleValue, $resource->getValue());
		$this->assertEquals("integer", $resource->getTypeHelper()->getName());
		$this->assertEquals("//resource/simple", $resource->getAddress()->getAsString());

		$resource = $objectProvider->getResource("resource/complex");
		$this->assertInstanceOf(ResolvedObject::class, $resource);
		$this->assertSame($complexValue, $resource->getValue());
		$this->assertEquals(Author::class, $resource->getTypeHelper()->getName());
		$this->assertEquals("//resource/complex", $resource->getAddress()->getAsString());
	}

	/**
	 * @expectedException 			\Light\ObjectAccess\Exception\ResourceException
	 * @expectedExceptionMessage	Resource must have an address that is a subclass of Light\ObjectService\Resource\Addressing\EndpointRelativeAddress
	 */
	public function testPublishResourceWithInvalidAddress()
	{
		$objectProvider = new DefaultObjectProvider($this->typeRegistry);
		$endpoint = Endpoint::create("//", $objectProvider);
		$objectProvider->setEndpoint($endpoint);

		$value = new Author(1010, "John Doe");
		$resource = $this->typeRegistry->resolveValue($value);
		$objectProvider->publishResource($resource);
	}

	public function testPublishResource()
	{
		$objectProvider = new DefaultObjectProvider($this->typeRegistry);
		$endpoint = Endpoint::create("//", $objectProvider);
		$objectProvider->setEndpoint($endpoint);

		$value = new Author(1010, "John Doe");
		$resource = ResolvedValue::create($this->typeRegistry->getComplexTypeHelper(Author::class),
										  $value,
										  EndpointRelativeAddress::create($endpoint, "author"),
										  Origin::unavailable());
		$objectProvider->publishResource($resource);

		$this->assertSame($resource, $objectProvider->getResource("author"));
	}
}
