<?php

namespace Light\ObjectService\Mockup;

use Light\ObjectService\EndpointRegistry;
use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Expression\FindContext;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Resource\Query\Scope;
use Light\ObjectService\Resource\Query\WhereExpression;
use Light\ObjectService\Service\Endpoint;
use Light\ObjectService\Service\Util\ExecutionParametersObject;
use Light\ObjectService\Transaction\Transaction;
use Light\ObjectService\Type\CollectionType;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Type\ComplexTypeInterfaces\Create;
use Light\ObjectService\Type\ComplexTypeInterfaces\CreationContext;
use Light\ObjectService\Type\ObjectProvider;

class EndpointSetup
{
	/** @var Endpoint */
	private $endpoint;
	/** @var EndpointRegistry */
	private $endpointRegistry;
	/** @var ExecutionParameters */
	private $executionParameters;

	/**
	 * @return EndpointRegistry
	 */
	public function __construct($baseUrl = "http://example.org/endpoint/")
	{
		$this->endpoint = Endpoint::create($baseUrl);
		$objectRegistry = $this->endpoint->getObjectRegistry();

		Database::initialize();

		$objectRegistry->addType(new PostModel());
		$objectRegistry->addType(TypeFactory::getCommentType());
		$objectRegistry->addType(new CommentCollectionType());
		$objectRegistry->publishCollection("blog/posts", new PostCollectionModel());

		$this->endpointRegistry = new EndpointRegistry();
		$this->endpointRegistry->addEndpoint($this->endpoint);

		$this->executionParameters = new ExecutionParametersObject();
		$this->executionParameters->setEndpointRegistry($this->endpointRegistry);
		$this->executionParameters->setTransaction(new Transaction());
	}

	/**
	 * @return Endpoint
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	/**
	 * @return EndpointRegistry
	 */
	public function getEndpointRegistry()
	{
		return $this->endpointRegistry;
	}

	/**
	 * @return ExecutionParameters
	 */
	public function getExecutionParameters()
	{
		return $this->executionParameters;
	}
}

class Database
{
	public static $posts = array();
	public static $comments = array();
	public static $authors = array();
	
	public static function initialize()
	{
		// Posts
		self::$posts = array(); 

		self::$posts[] = $model = new Post(141, "This is my first post");
		$model->tags = array("post", "interesting");
		
		self::$posts[] = $model = new Post(142, "Object service in practice");
		$model->tags = array("post", "story");
		
		// Comments
		self::$comments = array();
		
		self::$comments[] = $model = new Comment(101, "John Doe", "This looks good");
		$model->post_id = 141;

		self::$comments[] = $model = new Comment(102, "Mary Jane", "I concur");
		$model->post_id = 141;
	}
	
	public static function load($filename)
	{
		if (file_exists($filename))
		{
			$data = unserialize(file_get_contents($filename));
			self::$posts = $data->posts;
			self::$comments = $data->comments;
			self::$authors = $data->authors;
		}
		else
		{
			self::initialize();
		}
	}
	
	public static function save($filename)
	{
		$data = new \stdClass;
		$data->posts = self::$posts;
		$data->comments = self::$comments;
		$data->authors = self::$authors;
		
		file_put_contents($filename, serialize($data));
	}
}

class Post
{
	const CLASSNAME = __CLASS__;
	
	public $id;
	public $title;
	public $compact_title;
	public $tags = array();
	
	public function __construct($id = null, $title = null)
	{
		$this->id = $id;
		$this->title = $title;
	}
}

class Comment
{
	const CLASSNAME = __CLASS__;
	
	public $id;
	public $post_id;
	public $author;
	public $text;
	
	public function __construct($id, $author, $text)
	{
		$this->id = $id;
		$this->author = $author;
		$this->text = $text;
	}
}

class Author
{
	const CLASSNAME = __CLASS__;
	
	public $id;
	public $name;
	
	public function __construct($id, $name)
	{
		$this->id = $id;
		$this->name = $name;
	}
}

class PostCollectionModel extends ObjectProvider
{
	public function __construct()
	{
		parent::__construct(new PostModel());
		
		$this->getSpecification()
			 ->field("id")->type("int")->criterion()->done()
			 ->field("title")->type("string")->criterion()->done();
			 
	}
	
	public function find(Scope $scope, FindContext $context)
	{
		$models = Database::$posts;
	
		if ($context->getContextObject())
		{
			$model = $context->getContextObject();
			if (in_array($model, $this->models))
			{
				$models = array($model);
			}
			else
			{
				$models = array();
			}
		}

		$expr = $scope->getCompiledQuery();
		$expr->with("id", function(Criterion $value, $propertyName) use (&$models)
		{
			$models = array_filter($models, function($model) use ($value)
			{
				return $model->id == $value->getValue();
			});
		});
	
		return $models;
	}
}

class CommentCollectionType extends ObjectProvider
{
	public function __construct()
	{
		parent::__construct(TypeFactory::getCommentType());
		
		$this->getSpecification()
			 ->field("id")->criterion();
	
	}
	
	public function find(Scope $scope, FindContext $context)
	{
		$models = Database::$comments;
	
		if ($context->getContextObject())
		{
			$post_id = $context->getContextObject()->id;
			$models = array_filter($models, function($model) use ($post_id)
			{
				return $model->post_id == $post_id;
			});
		}

		$expr = $scope->getCompiledQuery();
		$expr->with("id", function(Criterion $value, $propertyName) use (&$models)
		{
			$models = array_filter($models, function($model) use ($value)
			{
				return $model->id == $value->getValue();
			});
		});

		return $models;
	}
}

class PostModel extends ComplexType implements Create
{
	const CLASSNAME = __CLASS__;

	public static $autoId = 4000;
	
	public function __construct()
	{
		parent::__construct();

		$this->getSpecification()
			 ->classname(Post::CLASSNAME)
			 ->field("id")->type("int")->primaryKey()->done()
			 ->field("title")->type("string")->done()
			 ->field("compact_title")->type("string")
			 ->getter(function(Post $post)
			 {
			 	if ($post->compact_title)
			 	{
			 		return $post->compact_title;
			 	}
			 	else
			 	{
					return str_replace(" ", "-", strtolower($post->title));
			 	}
			 })
			 ->setter(function(Post $post, $value, Transaction $tx)
			 {
				$post->compact_title = str_replace(" ", "-", strtolower($value));
			 })
			 ->done()
			 ->field("tags")->type("string[]")->done()
			 ->field("comments")->collectionOfType(Comment::CLASSNAME)->done();
	}

	/**
	 * Creates a new instance of an object of this complex-type.
	 * @param CreationContext $context
	 * @return object
	 */
	public function createObject(CreationContext $context)
	{
		return new Post(++self::$autoId);
	}
}

final class TypeFactory
{
	/**
	 * @return \Light\ObjectService\Type\ComplexType
	 */
	public static function getCommentType()
	{
		$type = new ComplexType();
		$type->getSpecification()
			 ->classname(Comment::CLASSNAME)
			 ->field("id")->type("int")->primaryKey()->done()
			 ->field("author")->type("string")->done()
			 ->field("text")->type("string")->done();
		return $type;
	}
}
