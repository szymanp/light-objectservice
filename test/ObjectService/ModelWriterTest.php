<?php

namespace Light\ObjectBroker;

use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\Mockup\Post;
use Light\ObjectService\Exceptions\ResolutionException;
use Light\ObjectService\Transaction\Transaction;
use Light\ObjectService\Model\ModelWriter;

require_once 'config.php';
require_once __DIR__ . '/MockupModel.php';

class ModelWriterTest extends \PHPUnit_Framework_TestCase
{
	private $tx, $model, $post;
	
	protected function setUp()
	{
		parent::setUp();

		$this->tx 		= new Transaction();
		$this->model	= new PostModel();
		$this->post 	= new Post(13, "My first post");
	}
	
	public function testRawProperty()
	{
		$writer = new ModelWriter($this->tx, $this->model, $this->post);
		$writer->setProperty("title", $newtitle = "My new post");
		
		$this->assertEquals($newtitle, $this->post->title);
		$this->assertContains($this->post, $this->tx->getDirty());
	}

	public function testSetterProperty()
	{
		$writer = new ModelWriter($this->tx, $this->model, $this->post);
		$writer->setProperty("compact_title", "My new post");
	
		$this->assertEquals("my-new-post", $this->post->compact_title);
		$this->assertContains($this->post, $this->tx->getDirty());
	}
	
    /**
     * @expectedException        Light\ObjectService\Exceptions\ResolutionException
     * @expectedExceptionMessage ::missing cannot be written
     */
	public function testMissingProperty()
	{
		$writer = new ModelWriter($this->tx, $this->model, $this->post);
		$writer->setProperty("missing", "abc");
	}
}
