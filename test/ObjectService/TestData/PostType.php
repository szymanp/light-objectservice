<?php
namespace Light\ObjectService\TestData;

use Light\Exception\InvalidParameterType;
use Light\ObjectAccess\Resource\Addressing\ResourceAddress;
use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectAccess\Type\Complex\CanonicalAddress;
use Light\ObjectAccess\Type\Complex\Create;
use Light\ObjectAccess\Type\Util\DefaultComplexType;
use Light\ObjectAccess\Type\Util\DefaultProperty;

class PostType extends DefaultComplexType implements Create, CanonicalAddress
{
	/** @var Database */
	private $database;

	/** @var ResourceAddress */
	private $canonicalBase;

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
	 * @param ResourceAddress $canonicalBase
	 */
	public function setCanonicalBase(ResourceAddress $canonicalBase)
	{
		$this->canonicalBase = $canonicalBase;
	}

	/**
	 * Creates a new instance of an object of this complex type.
	 * @param Transaction $transaction
	 * @return object
	 */
	public function createObject(Transaction $transaction)
	{
		return $this->database->createPost();
	}

	/**
	 * Returns a canonical address for the specified object.
	 * @param mixed $object
	 * @return ResourceAddress
	 */
	public function getCanonicalAddress($object)
	{
		if ($object instanceof Post)
		{
			return $this->canonicalBase->appendElement($object->getId());
		}
		else
		{
			throw new InvalidParameterType('$object', $object, Post::class);
		}
	}
}