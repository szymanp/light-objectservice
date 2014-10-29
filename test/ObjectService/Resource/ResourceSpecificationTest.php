<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Mockup\Database;
use Light\ObjectService\Mockup\EndpointSetup;
use Light\ObjectService\Resource\Addressing\ResourceIdentifier;

class ResourceSpecificationTest extends \PHPUnit_Framework_TestCase
{
	/** @var EndpointSetup */
	private $endpointSetup;

	protected function setUp()
	{
		parent::setUp();

		$this->endpointSetup = new EndpointSetup();
	}

	public function testExistingResourceSpecification()
	{
		$spec = new ExistingResourceSpecification(ResourceIdentifier::createFromUrl("http://example.org/endpoint/blog/posts/141"));
		$resolved = $spec->resolve($this->endpointSetup->getExecutionParameters());

		$this->assertNotNull($resolved);
		$this->assertSame($resolved->getValue(), Database::$posts[0]);
	}

}
 