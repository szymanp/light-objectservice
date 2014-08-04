<?php

namespace Light\ObjectService\Mockup;

use Light\ObjectService\Model\ObjectProvider;
use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Expression\FindContext;
use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Model\ComplexType;
use Light\ObjectService\Transaction\Transaction;
use Light\ObjectService\Model\CollectionType;

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
	
	public static function load()
	{
		// TODO
	}
	
	public static function save()
	{
		// TODO
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
	
	public function find(WhereExpression $expr, FindContext $context)
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
	
	public function find(WhereExpression $expr, FindContext $context)
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

class PostModel extends ComplexType
{
	const CLASSNAME = __CLASS__;
	
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
}

final class TypeFactory
{
	/**
	 * @return \Light\ObjectService\Model\ComplexType
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
