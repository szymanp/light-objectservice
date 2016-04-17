<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Projection\DataCollection;
use Light\ObjectService\Resource\Projection\DataObject;
use Light\ObjectService\TestData\Author;
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
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/my-first-post")
		);

		$data = $dataObject->getData();
		$data->id = 12345;
		$data->title = 'My first post';
		$data->text = 'Hello world!';

		$serializer = new HalSerializer();
		$result = $serializer->serializeStructure($dataObject);

		$expected = [
			'_links' => [
				'self' => ['href' => 'http://example.org/resources/my-first-post'],
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
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/my-first-post")
		);

		$subDataObject = new DataObject(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Author::class),
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
				'self' => ['href' => 'http://example.org/resources/my-first-post'],
				'type' => ['href' => 'php:Light\ObjectService\TestData\Post']
			],
			'_embedded' => [
				'author' => [
					'_links' => [
						'self' => ['href' => 'http://example.org/resources/max'],
						'type' => ['href' => 'php:Light\ObjectService\TestData\Author']
					],
					'id' => 12345
				]
			]
		];

		// Convert to an object
		$expected = json_decode(json_encode($expected), false);

		$this->assertEquals($expected, $result);
	}

	public function testObjectWithResourceList()
	{
		$dataObject = new DataObject(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Author::class),
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/max")
		);

		$collectionObject = new DataCollection(
			$this->setup->getTypeRegistry()->getCollectionTypeHelper(Post::class . '[]'),
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/max/posts")
		);

		$subDataObject1 = new DataObject(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class),
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/post-1")
		);
		$subDataObject1->getData()->title = "My first post";

		$subDataObject2 = new DataObject(
			$this->setup->getTypeRegistry()->getComplexTypeHelper(Post::class),
			EndpointRelativeAddress::create($this->setup->getEndpoint(), "resources/post-2")
		);
		$subDataObject2->getData()->title = "My second post";

		$collectionObject->setData([$subDataObject1, $subDataObject2]);

		$data = $dataObject->getData();
		$data->name = "Max Ray";
		$data->posts = $collectionObject;

		$serializer = new HalSerializer();
		$result = $serializer->serializeStructure($dataObject);

		$expected = [
			'_links' => [
				'self' => ['href' => 'http://example.org/resources/max'],
				'type' => ['href' => 'php:Light\ObjectService\TestData\Author']
			],
			'name' => 'Max Ray',
			'_embedded' => [
				'posts' => [
					'_links' => [
						'self' => ['href' => 'http://example.org/resources/max/posts'],
						'type' => ['href' => 'php:Light\ObjectService\TestData\Post[]']
					],
					'_embedded' => [
						'elements' => [
							[
								'_links' => [
									'self' => ['href' => 'http://example.org/resources/post-1'],
									'type' => ['href' => 'php:Light\ObjectService\TestData\Post']
								],
								'title' => 'My first post'
							],
							[
								'_links' => [
									'self' => ['href' => 'http://example.org/resources/post-2'],
									'type' => ['href' => 'php:Light\ObjectService\TestData\Post']
								],
								'title' => 'My second post'
							]
						]
					]
				]
			]
		];

		// Convert to an object
		$expected = json_decode(json_encode($expected), false);

		$this->assertEquals($expected, $result);
	}

}
