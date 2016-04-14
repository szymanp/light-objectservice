<?php
namespace Szyman\ObjectService\Request\Json;

use Light\ObjectService\Resource\Addressing\UrlUnresolvedAddress;
use Light\ObjectService\TestData\Author;
use Light\ObjectService\TestData\Post;
use Light\ObjectService\TestData\Setup;
use Szyman\ObjectService\Resource\ExistingResourceReference;
use Szyman\ObjectService\Resource\KeyValueComplexValueRepresentation;
use Szyman\ObjectService\Resource\NewComplexResourceReference;

class StandardJsonComplexValueDeserializerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		$this->setup = Setup::create();
	}

	/**
	 * @expectedException \Light\ObjectService\Exception\MalformedRequest
	 * @expectedExceptionMessage Could not convert request body to JSON:
	 */
	public function testInvalidJson()
	{
		// JSON only allows double quotes
		$json = "{'title': 'Great quotes'}";

		$deserializer = new StandardJsonComplexValueDeserializer($this->getComplexTypeHelper(Post::class));
		$deserializer->deserialize($json);
	}

	public function testTextFields()
	{
		$json = <<<'EOD'
{
"title": "Great quotes",
"text": "To be or not to be"
}
EOD;

		$deserializer = new StandardJsonComplexValueDeserializer($this->getComplexTypeHelper(Post::class));
		$result = $deserializer->deserialize($json);

		$this->assertInstanceOf(KeyValueComplexValueRepresentation::class, $result);
		$this->assertEquals([ 'title' => 'Great quotes', 'text' => 'To be or not to be'], $result->getValues());
	}

	public function testUrlReference()
	{
		$url = 'http://example.org/resources/max';

		$json = '{"author": { "_href": "' . $url . '"}}';

		$deserializer = new StandardJsonComplexValueDeserializer($this->getComplexTypeHelper(Post::class));
		$result = $deserializer->deserialize($json);

		$this->assertEquals([ 'author' => new ExistingResourceReference(new UrlUnresolvedAddress($url))], $result->getValues());
	}

	public function testNewObjectReference()
	{
		$json = <<<'EOD'
{
"author":
	{
	"name": "John Doe",
	"age": 23
	}
}
EOD;

		$deserializer = new StandardJsonComplexValueDeserializer($this->getComplexTypeHelper(Post::class));
		$result = $deserializer->deserialize($json);

		$this->assertInstanceOf(KeyValueComplexValueRepresentation::class, $result);
		$ref = $result->getValues()['author'];

		// NewComplexResourceReference does not have any getters, and we generally do not want them.
		// Therefore we use PHP object equality to test whether the result is as expected.
		$representation = new KeyValueComplexValueRepresentation();
		$representation->setValue("name", "John Doe");
		$representation->setValue("age", 23);
		$expected = new NewComplexResourceReference($this->getComplexTypeHelper(Author::class), $representation);
		$this->assertEquals($expected, $ref);
	}

	/**
	 * @return \Light\ObjectAccess\Type\ComplexTypeHelper
	 */
	private function getComplexTypeHelper($name)
	{
		return $this->setup->getTypeRegistry()->getComplexTypeHelper($name);
	}
}
