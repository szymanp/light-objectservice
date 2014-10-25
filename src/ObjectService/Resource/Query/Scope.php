<?php
namespace Light\ObjectService\Resource\Query;

use Light\ObjectService\Expression\WhereExpressionSource;

/**
 * Scope specifies the filtering restrictions and ordering of a collection.
 *
 * Scope can be created from an URL - in which case it comes in two forms:
 * - a path scope: http://hostname/endpoint/post/(count=5,offset=10)/title (via PathScopeParser)
 * - target scope: http://hostname/endpoint/post?count=5&offset=10 (via TargetScopeParser)
 * Scope can also be passed inside the body of the request. Then it is parsed by an appropriate format handler,
 * e.g. JsonScopeReader.
 */
class Scope
{
	// Scope is a container for restrictions and ordering on a collection.

	const WITH_INDEX = 1;
	const WITH_KEY = 2;
	const WITH_VALUE = 3;
	const WITH_QUERY = 4;

	/** @var integer */
	private $method;
	/** @var integer */
	private $index;
	/** @var string */
	private $key;
	/** @var mixed */
	private $value;
	/** @var WhereExpressionSource */
	private $query;

	/** @var integer */
	private $count;
	/** @var integer */
	private $offset;

	/**
	 * Returns the method to use for filtering the collection.
	 * @return int	One of the WITH_* constants.
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @return int
	 */
	public function getIndex()
	{
		return $this->index;
	}

	/**
	 * @param int $index
	 */
	public function setIndex($index)
	{
		$this->index = $index;
		$this->method = self::WITH_INDEX;
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
		$this->method == self::WITH_KEY;
	}

	/**
	 * @return WhereExpressionSource
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * @param WhereExpressionSource $query
	 */
	public function setQuery(WhereExpressionSource $query)
	{
		$this->query = $query;
		$this->method = self::WITH_QUERY;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
		$this->method = self::WITH_VALUE;
	}

	/**
	 * Returns the maximum number of elements to retrieve from the collection.
	 * @return int
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * @param int $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}

	/**
	 * Returns the offset of the first element to retrieve from the collection.
	 * @return int
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset)
	{
		$this->offset = $offset;
	}
} 