<?php 

namespace Light\ObjectService\Model;

use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Expression\FindContext;

abstract class ObjectProvider extends CollectionType
{
	/**
	 * Returns all objects matching the expression.
	 * @param WhereExpression $where
	 * @param FindContext	  $context
	 * @return object[]
	 */
	abstract public function find(WhereExpression $where, FindContext $context);
}