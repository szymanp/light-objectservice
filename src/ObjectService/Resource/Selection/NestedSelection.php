<?php
namespace Light\ObjectService\Resource\Selection;

use Light\ObjectAccess\Type\ComplexTypeHelper;

final class NestedSelection extends RootSelection
{
	/** @var Selection */
	private $parent;

	public function __construct(Selection $parent, ComplexTypeHelper $typeHelper)
	{
		parent::__construct($typeHelper);
		$this->parent = $parent;
	}
	
	// TODO This class should either contain a Query or a Scope.
	//		Which one should it be?
}