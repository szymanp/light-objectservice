<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectAccess\Type\Complex\Create;
use Light\ObjectAccess\Type\Util\DefaultComplexType;
use Light\ObjectAccess\Type\Util\DefaultProperty;

class PostType extends DefaultComplexType implements Create
{
	/** @var Database */
	private $database;

	public function __construct(Database $database)
	{
		parent::__construct(Post::class);

		$this->database = $database;

		$this->addProperty(new DefaultProperty("id", "int"));
		$this->addProperty(new DefaultProperty("title", "string"));
		$this->addProperty(new DefaultProperty("text", "string"));
		$this->addProperty(new DefaultProperty("author", Author::class));
	}

	/**
	 * Creates a new instance of an object of this complex type.
	 * @param Transaction $transaction
	 * @return object
	 */
	public function createObject(Transaction $transaction)
	{
		// TODO Fix transaction
		return $this->database->createPost();
	}

}