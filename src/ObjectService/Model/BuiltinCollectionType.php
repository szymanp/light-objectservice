<?php 

namespace Light\ObjectService\Model;

final class BuiltinCollectionType extends CollectionType
{
	public function __construct(Type $baseType)
	{
		parent::__construct($baseType);
	}
}