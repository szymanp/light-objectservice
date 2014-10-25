<?php
namespace Light\ObjectBroker\Service;

use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Service\Json\JsonRequestReader;
use Light\Util\HTTP\Request as HTTPRequest;

require_once 'config.php';
require_once __DIR__ . '/../MockupModel.php';

class JsonRequestReaderTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Light\ObjectService\Service\Json\JsonRequestReader */
	private $reader;
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->reader = new JsonRequestReader("/api/");
	}
	
	public function testIsAcceptable()
	{
		$this->assertTrue($this->reader->isAcceptable($this->getMockRequest("POST", "/api/b")));
		$this->assertTrue($this->reader->isAcceptable($this->getMockRequest("GET", "/api/b")));
		$this->assertFalse($this->reader->isAcceptable($this->getMockRequest("POST", "/api/b", "", "text/xml")));
	}
	
	public function testReadRequest()
	{
		$httpRequest = $this->getMockRequest("GET", "/api/post");
		$svcRequest  = $this->reader->read($httpRequest);
		
		$this->assertInstanceOf("Light\ObjectService\Service\Request\Request", $svcRequest);
		$this->assertNotNull($svcRequest->getResourceSpecification());
		$this->assertNull($svcRequest->getResourceSpecification()->getOperation());
		$this->assertEquals("post", $svcRequest->getResourceSpecification()->getUrl());
	}
	
	public function testReadRequestWithQuery()
	{
		$body = <<<DOC
		{
			"query": {
				"target": {
					"id": 5
				}
			},
			"method": "GET"
		}
DOC;
		$httpRequest = $this->getMockRequest("POST", "/api/post", $body);
		$svcRequest  = $this->reader->read($httpRequest);
	
		$this->assertInstanceOf("Light\ObjectService\Service\Request\Request", $svcRequest);
		$this->assertNotNull($svcRequest->getResourceSpecification());
		$this->assertNull($svcRequest->getResourceSpecification()->getOperation());
		$this->assertEquals("post", $svcRequest->getResourceSpecification()->getUrl());
		
		$targetWhere = $svcRequest->getResourceSpecification()->getQuery(PathExpression::TARGET);
		$this->assertInstanceOf("Light\ObjectService\Expression\WhereExpressionSource", $targetWhere);
	}
	
	/**
	 * @return \Light\Util\HTTP\Request
	 */
	protected function getMockRequest($method, $uri, $body = null, $ct = "application/json")
	{
		$server = array();
		$server['REQUEST_METHOD'] = $method;
		$server['REQUEST_URI'] = $uri;
		if ($method != "GET")
		{
			$server['CONTENT_TYPE'] = $ct;
		}
		
		$request = new HTTPRequest($server);
		
		if (is_string($body))
		{
			$request->setBody($body);
		}
		else if (is_object($body))
		{
			$request->setBody(json_encode($body));
		}
		
		return $request;
	}
}

