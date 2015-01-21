<?php
namespace Light\ObjectService\TestData;

use Light\Exception\NotImplementedException;
use Light\ObjectAccess\Resource\Origin_PropertyOfObject;
use Light\ObjectAccess\Resource\Origin_Unavailable;
use Light\ObjectAccess\Resource\ResolvedCollection;
use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Resource\ResolvedCollectionValue;
use Light\ObjectAccess\Type\Collection\Iterate;
use Light\ObjectAccess\Type\Util\CollectionPropertyHost;
use Light\ObjectAccess\Type\Util\DefaultCollectionType;
use Light\ObjectAccess\Type\Util\DefaultFilterableProperty;

class PostCollectionType extends DefaultCollectionType implements Iterate
{
	/** @var Database */
	private $database;

	/** @var CollectionPropertyHost */
	private $properties;

	public function __construct(Database $db)
	{
		parent::__construct(Post::class);
		$this->database = $db;
		$this->properties = new CollectionPropertyHost();
		$this->properties->append(new DefaultFilterableProperty("author", Author::class));
	}

	/**
	 * Returns an Iterator over the elements in the given collection.
	 * @param ResolvedCollection $collection
	 * @return \Iterator
	 * @throws NotImplementedException
	 */
	public function getIterator(ResolvedCollection $collection)
	{
		if ($collection instanceof ResolvedCollectionValue)
		{
			return new \ArrayIterator($collection->getValue());
		}
		elseif ($collection instanceof ResolvedCollectionResource)
		{
			$origin = $collection->getOrigin();
			if ($origin instanceof Origin_Unavailable)
			{
				return new \ArrayIterator($this->database->getPosts());
			}
			elseif ($origin instanceof Origin_PropertyOfObject)
			{
				$object = $origin->getObject()->getValue();
				if ($origin->getPropertyName() == "posts" && $object instanceof Author)
				{
					return new \ArrayIterator($this->database->getPostsForAuthor($object));
				}
			}
			throw new NotImplementedException("Unknown origin");
		}
		throw new \LogicException("Unknown class");
	}
}