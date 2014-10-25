<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Type\ResolvedValue;

class NewResourceSpecification extends ResourceSpecification
{
	/** @var ComplexType */
	private $complexType;

	/**
	 * @param ExecutionParameters $parameters
	 * @return ResolvedValue
	 */
	public function resolve(ExecutionParameters $parameters)
	{
		// TODO: Implement resolve() method.
	}
}