<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Resource\Addressing\ResourceAddress;
use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectAccess\Type\Complex\CanonicalAddress;
use Light\ObjectAccess\Type\Complex\Create;
use Light\ObjectAccess\Type\Util\DefaultComplexType;
use Light\ObjectAccess\Type\Util\DefaultProperty;
use Szyman\Exception\Exception;
use Szyman\Exception\InvalidArgumentTypeException;

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
		return new Post();
	}

	/**
	 * Returns a canonical address for the specified object.
	 * @param mixed $object
	 * @return ResourceAddress
	 */
	public function getCanonicalAddress($object)
	{
		if (is_null($this->canonicalBase))
		{
			throw new Exception("Canonical base has not been set");
		}

		if ($object instanceof Post)
		{
			return $this->canonicalBase->appendElement($object->getId());
		}
		else
		{
			throw new InvalidArgumentTypeException('$object', $object, Post::class);
		}
	}
}