<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectAccess\Type\Complex\Create;
use Light\ObjectAccess\Type\Util\CollectionResourceProperty;
use Light\ObjectAccess\Type\Util\DefaultComplexType;
use Light\ObjectAccess\Type\Util\DefaultProperty;

class AuthorType extends DefaultComplexType implements Create
{
	/** @var Database */
	private $database;

	public function __construct(Database $database)
	{
		parent::__construct(Author::class);
		$this->database = $database;
		$this->addProperty(new DefaultProperty("id", "int"));
		$this->addProperty(new DefaultProperty("name", "string"));
		$this->addProperty(new DefaultProperty("age", "int"));
		$this->addProperty(new CollectionResourceProperty("posts", Post::class . "[]"));
	}

	/**
	 * @inheritdoc
	 */
	public function createObject(Transaction $transaction)
	{
		// TODO Fix transaction
		return $this->database->createAuthor();
	}


}