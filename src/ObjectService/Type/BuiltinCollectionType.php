<?php 

namespace Light\ObjectService\Type;

final class BuiltinCollectionType extends CollectionType
{
	public function __construct(Type $baseType)
	{
		parent::__construct($baseType);
	}
}