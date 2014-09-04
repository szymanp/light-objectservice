<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Mockup\Database;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Service\Util\ExecutionParametersObject;
use Light\ObjectService\Util\DefaultNameRegistry;

require_once 'config.php';
require_once __DIR__ . '/../MockupModel.php';

class ResourceSpecificationTest extends \PHPUnit_Framework_TestCase
{
	/** @var ExecutionParameters */
	private $parameters;

	protected function setUp()
	{
		parent::setUp();

		$nameRegistry = new DefaultNameRegistry();
		$nameRegistry->setResourceBaseUri("http://example.org/");

		$this->parameters = new ExecutionParametersObject();
		$this->parameters->setObjectRegistry($objectRegistry = new ObjectRegistry());
		$objectRegistry->setNameRegistry($nameRegistry);

		Database::initialize();
		$objectRegistry->addType($postModel = new PostModel());
		$objectRegistry->publishCollection("models/post", $this->postCollectionModel = new PostCollectionModel());
	}

	public function testUrlResourceSpecification()
	{
		$spec = new UrlResourceSpecification();
		$spec->setUrl("http://example.org/models/post/141");
		$result = $spec->execute($this->parameters);

		$this->assertInstanceOf('Light\ObjectService\Resource\ResourceSpecificationResult', $result);
		$this->assertSame(Database::$posts[0], $result->getTargetResource()->getValue());
		$this->assertSame(Database::$posts[0], $result->getBaseResource()->getValue());
	}

}

