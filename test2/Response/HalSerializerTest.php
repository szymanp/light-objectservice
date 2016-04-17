<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Projection\DataObject;
use Light\ObjectService\TestData\Setup;
use Light\ObjectService\TestData\Post;

class HalSerializerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Setup */
	private $setup;

	protected function setUp()
	{
		$this->setup = Setup::create();
	}

	public function testObjectWithScalars()
	{
		$dataObject = new DataObject(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class),
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/john")
		);

		$data = $dataObject->getData();
		$data->id = 12345;
		$data->title = 'My first post';
		$data->text = 'Hello world!';

		$serializer = new HalSerializer();
		$result = $serializer->serializeStructure($dataObject);

		$expected = [
			'_links' => [
				'self' => ['href' => 'http://example.org/resources/john'],
				'type' => ['href' => 'php:Light\ObjectService\TestData\Post']
			],
			'id' 	=> $data->id,
			'title'	=> $data->title,
			'text'	=> $data->text
		];

		// Convert to an object
		$expected = json_decode(json_encode($expected), false);

		$this->assertEquals($expected, $result);
	}

	public function testObjectWithResource()
	{
		$dataObject = new DataObject(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class),
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/john")
		);

		$subDataObject = new DataObject(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class),
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/max")
		);


		$data = $dataObject->getData();
		$data->author = $subDataObject;

		$data = $subDataObject->getData();
		$data->id = 12345;

		$serializer = new HalSerializer();
		$result = $serializer->serializeStructure($dataObject);

		$expected = [
			'_links' => [
				'self' => ['href' => 'http://example.org/resources/john'],
				'type' => ['href' => 'php:Light\ObjectService\TestData\Post']
			],
			'_embedded' => [
				'author' => [
					'_links' => [
						'self' => ['href' => 'http://example.org/resources/max'],
						'type' => ['href' => 'php:Light\ObjectService\TestData\Post']
					],
					'id' => 12345
				]
			]
		];

		// Convert to an object
		$expected = json_decode(json_encode($expected), false);

		$this->assertEquals($expected, $result);
	}
}
