<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Service\Request\Operation;

/**
 * Reads a path expression from JSON data.
 *
 */
final class JsonPathExpressionReader
{
	/**
	 * Reads a path expression from JSON data. 
	 * @param string	$href
	 * @param \stdClass $queries
	 * @param Operation $parent
	 * @return \Light\ObjectService\Expression\PathExpression
	 */
	public static function read($href, \stdClass $queries = null, Operation $parent)
	{
		$parentPath = $parent->getResourcePath();
		
		if (empty($href))
		{
			return $parentPath;
		}
		
		$reader = new self($href, $queries, $parentPath);
		return $reader->readPathExpression();
	}
	
	/** @var \Light\ObjectService\Expression\PathExpression */
	private $pathExpr;
	
	public function __construct($href, \stdClass $queries = null, PathExpression $parent = null)
	{
		$this->pathExpr = new PathExpression();
		$this->pathExpr->setPath($href);
		if ($parent)
		{
			$this->pathExpr->setRelativeTo($parent);
		}
		
		if ($queries)
		{
			foreach($queries as $name => $query)
			{
				$this->pathExpr->setWhereReference($name, JsonWhereExpressionSource::create($query));
			}
		}
	}

	/**
	 * Reads a PathExpression from the provided data.
	 * @return \Light\ObjectService\Expression\PathExpression
	 */
	public function readPathExpression()
	{
		return $this->pathExpr;
	}
}