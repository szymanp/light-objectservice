<?php
namespace Light\ObjectService\Service;

require_once 'config.php';
require_once __DIR__ . '/../MockupModel.php';
require_once __DIR__ . "/JsonClient.php";

class RemoteJsonTest extends \PHPUnit_Framework_TestCase
{
	private $client;
	
	protected function setUp()
	{
		parent::setUp();

		$this->client = new JsonClient("ObjectService/Service/JsonTestService.php");
		
		if (!$this->client->isAvailable())
		{
			$this->markTestSkipped("Missing test.url setup in phpunit.xml");
		}
	}	
	
	public function testReadPostViaId()
	{
		$result = $this->client->get("/post/141");
		
		$meta = $result['meta'];
		$data = $result['data'];
		
		$this->assertEquals("//mockup/Post#complex", $meta['rel']);
		$this->assertEquals(141, $data['id']);
		$this->assertTrue(isset($data['title']));
		$this->assertTrue(isset($data['comments']));
		$this->assertTrue(isset($data['compact_title']));
	}
	
	public function testUpdatePostViaId()
	{
		$title = "This is my " . rand(1, 200) . ". post";
		
		$body = array( "data" => array( "title" => $title ));
		$result = $this->client->put("/post/141", $body);
		
		$data = $result['data'];
		$meta = $result['meta'];

		$this->assertEquals("//mockup/Post#complex", $meta['rel']);
		$this->assertEquals(141, $data['id']);
		$this->assertEquals($title, $data['title']);
	}
}

