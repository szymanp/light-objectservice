<?php
namespace Light\ObjectService\Resource\Selection;

use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Type\CollectionTypeHelper;

final class NestedCollectionSelection extends RootSelection
{
	/** @var Selection */
	private $parent;

	/** @var CollectionTypeHelper */
	private $collectionTypeHelper;

	/** @var Scope */
	private $scope;

	public function __construct(Selection $parent, CollectionTypeHelper $typeHelper)
	{
		parent::__construct($typeHelper->getBaseTypeHelper());
		$this->collectionTypeHelper = $typeHelper;
		$this->parent = $parent;
	}

	/**
	 * Sets the scope to be used when reading elements from the collection.
	 * @param Scope $scope
	 * @return $this
	 */
	public function setScope(Scope $scope)
	{
		$this->scope = $scope;
		return $this;
	}

	/**
	 * Returns the scope to be used when reading elements from this collection.
	 * @return Scope	A Scope object, if set; otherwise, NULL.
	 */
	public function getScope()
	{
		return $this->scope;
	}
}