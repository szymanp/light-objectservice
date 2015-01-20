<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Type\Util\DefaultComplexType;
use Light\ObjectAccess\Type\Util\DefaultProperty;

class PostType extends DefaultComplexType
{
	public function __construct()
	{
		parent::__construct(Post::class);

		$this->addProperty(new DefaultProperty("id", "int"));
		$this->addProperty(new DefaultProperty("title", "string"));
		$this->addProperty(new DefaultProperty("text", "string"));
		$this->addProperty(new DefaultProperty("author", Author::class));
	}
}