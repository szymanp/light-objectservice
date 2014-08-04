<?php

namespace Light\ObjectBroker;

use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\Model\ModelReader;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Exceptions\ResolutionException;

require_once 'config.php';
require_once __DIR__ . '/MockupModel.php';

class ModelReaderTest extends \PHPUnit_Framework_TestCase
{
	public function testModelReaderWithObject()
	{
		$model = new PostModel();
		$post = new Post();
		$post->id 		= 13;
		$post->title 	= "My first post";
		
		$reader = new ModelReader($model, $post);
		
		$this->assertEquals($post->id, $reader->readProperty("id"));
		$this->assertEquals($post->title, $reader->readProperty("title"));
		$this->assertEquals("my-first-post", $reader->readProperty("compact_title"));
		
		try
		{
			$caught = false;
			$reader->readProperty("missing");
		}
		catch (ResolutionException $e)
		{
			$caught = true;
		}
		$this->assertTrue($caught);
	}
	
	public function testModelReaderWithArray()
	{
		$model = new PostModel();
		$post = array();
		$post['id'] 		= 13;
		$post['title']		= "My first post";
		
		$reader = new ModelReader($model, $post);
		
		$this->assertEquals($post['id'], $reader->readProperty("id"));
		$this->assertEquals($post['title'], $reader->readProperty("title"));
	}
}
