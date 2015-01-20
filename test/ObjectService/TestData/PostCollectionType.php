<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Type\Util\DefaultCollectionType;

class PostCollectionType extends DefaultCollectionType
{
	public function __construct()
	{
		parent::__construct(Post::class);
	}
}