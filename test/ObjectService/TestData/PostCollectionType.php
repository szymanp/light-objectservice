<?php
namespace Light\ObjectService\TestData;

use Szyman\Exception\NotImplementedException;
use Light\ObjectAccess\Query\Scope\QueryScope;
use Light\ObjectAccess\Resource\Origin_PropertyOfObject;
use Light\ObjectAccess\Resource\Origin_Unavailable;
use Light\ObjectAccess\Resource\ResolvedCollection;
use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Resource\ResolvedCollectionValue;
use Light\ObjectAccess\TestData\QueryFilterIterator;
use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectAccess\Type\Collection\Append;
use Light\ObjectAccess\Type\Collection\Element;
use Light\ObjectAccess\Type\Collection\Iterate;
use Light\ObjectAccess\Type\Collection\Property;
use Light\ObjectAccess\Type\Collection\Search;
use Light\ObjectAccess\Type\Collection\SearchContext;
use Light\ObjectAccess\Type\Collection\SetElementAtKey;
use Light\ObjectAccess\Type\Util\CollectionPropertyHost;
use Light\ObjectAccess\Type\Util\DefaultCollectionType;
use Light\ObjectAccess\Type\Util\DefaultFilterableProperty;

class PostCollectionType extends DefaultCollectionType implements Iterate, Append, Search, SetElementAtKey
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
	 * @param ResolvedCollectionValue $collection
	 * @return \Iterator	An iterator over the elements in the collection.
	 *                   	The key of the iterator should indicate the key of the object in the collection.
	 */
	public function getIterator(ResolvedCollectionValue $collection)
	{
		return new \ArrayIterator($collection->getValue());
	}

	/**
	 * Returns all the elements of the collection.
	 *
	 * This method will be called if all the elements of a collection need to be retrieved,
	 * for example when a search using {@link EmptyScope} is invoked.
	 *
	 * @param ResolvedCollectionResource $collection
	 * @return mixed	All the elements of the collection.
	 */
	public function read(ResolvedCollectionResource $collection)
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
			throw new NotImplementedException("Unknown origin (property = " . $origin->getPropertyName() . ", object = " . get_class($object) . ")");
		}
		throw new NotImplementedException("Unknown origin");
	}

	/**
	 * Appends a value to the collection
	 * @param ResolvedCollection $collection
	 * @param mixed              $value
	 * @param Transaction        $transaction
	 */
	public function appendValue(ResolvedCollection $collection, $value, Transaction $transaction)
	{
		// If the Post does not have an ID, then we generate a new one.
		if (is_null($value->getId()))
		{
			$value->setId($this->database->getNextPostId());
		}

		$this->savePostToDatabase($collection, $value);
	}
	
	protected function savePostToDatabase(ResolvedCollection $collection, Post $value)
	{
		$origin = $collection->getOrigin();
		if ($origin instanceof Origin_Unavailable)
		{
			$this->database->addPost($value);
		}
		else if ($origin instanceof Origin_PropertyOfObject)
		{
			$object = $origin->getObject()->getValue();
			if ($origin->getPropertyName() == "posts" && $object instanceof Author)
			{
				$value->setAuthor($object);
				$this->database->addPost($value);
			}
		}
	
	}

	protected function getElementAtKeyFromResource(ResolvedCollectionResource $coll, $key)
	{
		$origin = $coll->getOrigin();
		$post = $this->database->getPost($key);

		if (is_null($post))
		{
			return Element::notExists();
		}

		if ($origin instanceof Origin_Unavailable)
		{
			return Element::valueOf($post);
		}
		elseif ($origin instanceof Origin_PropertyOfObject)
		{
			$object = $origin->getObject()->getValue();
			if ($origin->getPropertyName() == "posts" && $object instanceof Author)
			{
				return $post->getAuthor() === $object ? Element::valueOf($post) : Element::notExists();
			}
		}
	}

	/**
	 * Returns a specification of a collection property.
	 * @param string $propertyName
	 * @return Property    A Property object, if the property exists; otherwise, NULL.
	 */
	public function getProperty($propertyName)
	{
		return $this->properties[$propertyName];
	}

	/**
	 * Returns all elements of the collection matching the query scope.
	 * @param ResolvedCollectionResource $collection
	 * @param QueryScope                 $scope
	 * @param SearchContext              $context
	 * @return mixed    Elements of the collection matching the scope.
	 */
	public function find(ResolvedCollectionResource $collection, QueryScope $scope, SearchContext $context)
	{
		$offset = $scope->getOffset() ?: 0;
		$count = $scope->getCount() ?: -1;

		$innerIterator = new \ArrayIterator($this->read($collection));
		$iterator = new \LimitIterator(new QueryFilterIterator($innerIterator, $scope->getQuery()), $offset, $count);
		return iterator_to_array($iterator);
	}
	
	public function setElementAtKey(ResolvedCollection $collection, $key, $value, Transaction $transaction)
	{
		$value->setId($key);
		$this->savePostToDatabase($collection, $value);
	}
}
