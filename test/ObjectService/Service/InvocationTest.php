<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Mockup\Author;
use Light\ObjectService\Mockup\AuthorType;
use Light\ObjectService\Mockup\Database;
use Light\ObjectService\Mockup\EndpointSetup;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Resource\Addressing\ResourceIdentifier;
use Light\ObjectService\Resource\NewResourceSpecification;
use Light\ObjectService\Resource\Operation\AppendOperation;
use Light\ObjectService\Resource\Operation\ResourceUpdateSpecification;
use Light\ObjectService\Resource\Operation\UpdateOperation;
use Light\ObjectService\Service\Request\RequestObject;
use Light\ObjectService\Service\Response\DataCollection;
use Light\ObjectService\Service\Response\DataObject;
use Light\ObjectService\Service\Util\InvocationParametersObject;

class InvocationTest extends \PHPUnit_Framework_TestCase
{
	/** @var EndpointSetup */
	private $endpointSetup;
	/** @var InvocationParametersObject */
	private $invocationParameters;

	protected function setUp()
	{
		parent::setUp();

		$this->endpointSetup = new EndpointSetup();

		$this->invocationParameters = $parameters = new InvocationParametersObject();
		$parameters->copyFrom($this->endpointSetup->getExecutionParameters());
	}

	/**
	 * GET http://example.org/endpoint/blog/posts/141
	 */
	public function testReadResource()
	{
		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts/141"));
		$response = new MockupResponse();

		$invocation = new Invocation($this->invocationParameters, $request, $response);
		$invocation->invoke();

		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
		$this->assertInstanceOf(DataObject::class, $response->result);

		$data = $response->result->getData();
		$this->assertEquals(141, $data->id);
		$this->assertEquals("This is my first post", $data->title);
		$this->assertEquals("this-is-my-first-post", $data->compact_title);
		$this->assertInstanceOf(DataCollection::class, $data->tags);
	}

	/**
	 * PUT http://example.org/endpoint/blog/posts/141
	 */
	public function testUpdateResource()
	{
		$updateSpec = new ResourceUpdateSpecification();
		$updateSpec->setValue("title", "Updated title");
		$updateOperation = new UpdateOperation($updateSpec);

		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts/141"));
		$request->addOperation($updateOperation);
		$response = new MockupResponse();

		$invocation = new Invocation($this->invocationParameters, $request, $response);
		$invocation->invoke();

		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
		$this->assertInstanceOf(DataObject::class, $response->result);

		$data = $response->result->getData();
		$this->assertEquals(141, $data->id);
		$this->assertEquals("Updated title", $data->title);
		$this->assertEquals("updated-title", $data->compact_title);
	}

	/**
	 * PUT http://example.org/endpoint/blog/posts/141
	 *
	 * Creates a new Author object and assigns it to Post->author.
	 */
	public function testUpdateResourceWithAnObject()
	{
		// Author
		$authorType = $this->endpointSetup->getEndpoint()->getObjectRegistry()->getType(Author::CLASSNAME);

		$authorUpdateSpec = new ResourceUpdateSpecification();
		$authorUpdateSpec->setValue("name", "John Williams");
		$newAuthorSpec = new NewResourceSpecification($authorType, $authorUpdateSpec);

		// Post
		$updateSpec = new ResourceUpdateSpecification();
		$updateSpec->setValue("title", "Updated title");
		$updateSpec->setResource("author", $newAuthorSpec);
		$updateOperation = new UpdateOperation($updateSpec);

		// Request and Response
		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts/141"));
		$request->addOperation($updateOperation);
		$response = new MockupResponse();

		// Invocation
		$invocation = new Invocation($this->invocationParameters, $request, $response);
		$invocation->invoke();

		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
		$this->assertInstanceOf(DataObject::class, $response->result);

		$data = $response->result->getData();
		$this->assertEquals(141, $data->id);
		$this->assertEquals("Updated title", $data->title);

		$authorData = $data->author->getData();
		$this->assertEquals(AuthorType::$autoId, $authorData->id);
		$this->assertEquals("John Williams", $authorData->name);
	}

	/**
	 * Try to read a collection resource.
	 * This should fail as a collection in itself is not readable.
	 */
	public function testReadCollectionAsResource()
	{
		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts"));
		$response = new MockupResponse();

		$invocation = new Invocation($this->invocationParameters, $request, $response);
		$invocation->invoke();

		// TODO Maybe this should return a 405 - Method Not Allowed as a GET on a collection is invalid.

		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
	}

	public function testReadCollectionContents()
	{
		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts/"));
		$response = new MockupResponse();

		$invocation = new Invocation($this->invocationParameters, $request, $response);
		$invocation->invoke();

		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
		$this->assertInstanceOf(DataCollection::class, $response->result);

		$data = $response->result->getData();
		$this->assertTrue(is_array($data));
		$this->assertEquals(2, count($data));
		$this->assertEquals(141, $data[0]->getData()->id);
		$this->assertEquals(142, $data[1]->getData()->id);
	}

	/**
	 * Try to update a collection resource.
	 * This should fail as a collection in itself is not updateable.
	 */
	public function testUpdateCollectionAsResource()
	{
		$updateSpec = new ResourceUpdateSpecification();
		$updateSpec->setValue("title", "Updated title");
		$updateOperation = new UpdateOperation($updateSpec);

		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts"));
		$request->addOperation($updateOperation);
		$response = new MockupResponse();

		$invocation = new Invocation($this->invocationParameters, $request, $response);
		$invocation->invoke();

		// TODO This should return some kind of error (OperationNotAllowed).
		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
	}

	/**
	 * When executing an update on a resolved collection, this operation should be executed
	 * on each of the elements in the collection separately.
	 */
	public function testUpdateCollectionElements()
	{
		$updateSpec = new ResourceUpdateSpecification();
		$updateSpec->setValue("title", "Updated title");
		$updateOperation = new UpdateOperation($updateSpec);

		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts/"));
		$request->addOperation($updateOperation);
		$response = new MockupResponse();

		$invocation = new Invocation($this->invocationParameters, $request, $response);
		$invocation->invoke();

		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
		$this->assertInstanceOf(DataCollection::class, $response->result);

		$data = $response->result->getData();
		$this->assertTrue(is_array($data));
		$this->assertEquals(2, count($data));
		$this->assertEquals(141, $data[0]->getData()->id);
		$this->assertEquals(142, $data[1]->getData()->id);
		$this->assertEquals("Updated title", $data[0]->getData()->title);
		$this->assertEquals("Updated title", $data[1]->getData()->title);
	}

	public function testAppendToCollection()
	{
		// Post
		$postType = $this->endpointSetup->getEndpoint()->getObjectRegistry()->getType(Post::CLASSNAME);

		$updateSpec = new ResourceUpdateSpecification();
		$updateSpec->setValue("title", "Appended post");

		$subject = new NewResourceSpecification($postType, $updateSpec);
		$appendOperation = new AppendOperation($subject);

		$request = new RequestObject();
		$request->setResourceIdentifier(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts"));
		$request->addOperation($appendOperation);
		$response = new MockupResponse();

		$invocation = new Invocation($this->invocationParameters, $request, $response);
		$invocation->invoke();

		// TODO What should the result be? The appended object? The whole collection?
//		$this->assertEquals(MockupResponse::SEND_ENTITY, $response->method);
//		$this->assertInstanceOf(DataCollection::class, $response->result);

		$this->assertEquals(3, Database::$posts);
		$this->assertEquals("Appended post", Database::$posts[2]->title);
	}
}
 