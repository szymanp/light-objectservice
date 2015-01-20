<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Type\Util\DefaultComplexType;
use Light\ObjectAccess\Type\Util\DefaultProperty;

class AuthorType extends DefaultComplexType
{
	public function __construct()
	{
		parent::__construct(Author::class);
		$this->addProperty(new DefaultProperty("id", "int"));
		$this->addProperty(new DefaultProperty("name", "string"));
		$this->addProperty(new DefaultProperty("age", "int"));

	}
}