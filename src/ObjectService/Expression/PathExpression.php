<?php

namespace Light\ObjectService\Expression;

/**
 * A path to a value in the object-property chain. 
 *
 * The path can have one of the following forms:
 * - a provider (e.g. models/user)
 * 	 the result is 
 * - a provider + an inline identifier (e.g. models/user/113)
 * - a provider + a where reference (e.g. models/user/_1)
 * - a provider + an identifier/reference + a property string (e.g. models/user/113/emails/_2)
 */
class PathExpression
{
	const TARGET = "target";
	
	/** @var string */
	private $path;

	/** @var array<string, WhereExpressionSource> */
	private $where = array();
	
	/** @var PathExpression */
	private $relativeTo;
	
	/**
	 * Sets the path to the target value. 
	 * @param string	$path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}
	
	/**
	 * Sets the where expression for a part of the path.
	 * @param string 					$ref
	 * @param WhereExpressionSource 	$where
	 */
	public function setWhereReference($ref, WhereExpressionSource $where)
	{
		$this->where[$ref] = $where;
	}
	
	/**
	 * Sets the path that this one is relative to.
	 * @param PathExpression $pathExpr
	 */
	public function setRelativeTo(PathExpression $pathExpr)
	{
		$this->relativeTo = $pathExpr;
	}
	
	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Returns a named where expression.
	 * @param unknown $ref
	 * @return \Light\ObjectService\Expression\WhereExpressionSource
	 */
	public function getWhereReference($ref)
	{
		return $this->where[$ref];
	}
	
	/**
	 * Returns the path that this one is relative to.
	 * @return \Light\ObjectService\Expression\PathExpression
	 */
	public function getRelativeTo()
	{
		return $this->relativeTo;
	}
	
	/**
	 * Copies data from another path object.
	 * @param PathExpression $path
	 */
	public function copyFrom(PathExpression $path)
	{
		$this->path 		= $path->path;
		$this->where		= $path->where;
		$this->relativeTo	= $path->relativeTo;
	}
}