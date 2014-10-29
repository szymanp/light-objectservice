<?php 

namespace Light\ObjectService\Type;

use Light\ObjectService\Expression\FindContext;
use Light\ObjectService\Resource\Query\Scope;

abstract class ObjectProvider extends CollectionType
{
	/**
	 * Returns all objects matching the scope.
	 * @param Scope			$scope
	 * @param FindContext	$context
	 * @return object[]
	 */
	abstract public function find(Scope $scope, FindContext $context);
}