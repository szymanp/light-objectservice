<?php
namespace Light\ObjectService\Resource\Selection;

use Light\ObjectAccess\Type\ComplexTypeHelper;

final class NestedComplexSelection extends RootSelection
{
	/** @var Selection */
	private $parent;

	public function __construct(Selection $parent, ComplexTypeHelper $typeHelper)
	{
		parent::__construct($typeHelper);
		$this->parent = $parent;
	}
}