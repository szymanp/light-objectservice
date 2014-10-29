<?php

namespace Light\ObjectService\Expression;

use Light\ObjectService\Exceptions\TypeException;
use Light\ObjectService\Resource\Query\WhereExpression;
use Light\ObjectService\Type\ComplexType;

final class NestedSelectExpression extends SelectExpression
{
	/** @var SelectExpression */
	private $parent;
	
	/** @var WhereExpression */
	private $where;
	
	public function __construct(SelectExpression $parent, ComplexType $type)
	{
		parent::__construct($type);
		$this->parent = $parent;
	}
	
	/**
	 * Sets the WhereExpression for this nested selection.
	 * @param WhereExpression $where
	 * @return \Light\ObjectService\Expression\NestedSelectExpression
	 */
	final public function where(WhereExpression $where)
	{
		if ($where->getType()->getBaseType() !== $this->getType())
		{
			throw new TypeException("Type of select expression does not match type of where expression");
		}
		$this->where = $where;
		return $this;
	}
	
	/**
	 * @return \Light\ObjectService\Expression\SelectExpression
	 */
	final public function done()
	{
		return $this->parent;
	}
	
	/**
	 * @return \Light\ObjectService\Resource\Query\WhereExpression
	 */
	final public function getWhereExpression()
	{
		return $this->where;
	}
}