<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Type\ResolvedValue;

class ResourceSpecificationResult
{
	/** @var \Light\ObjectService\Type\ResolvedValue */
	private $base;
	/** @var \Light\ObjectService\Type\ResolvedValue */
	private $target;

	/**
	 * @param \Light\ObjectService\Type\ResolvedValue $base
	 */
	final public function setBaseResource(ResolvedValue $base)
	{
		$this->base = $base;
	}

	/**
	 * @param \Light\ObjectService\Type\ResolvedValue $target
	 */
	final public function setTargetResource(ResolvedValue $target)
	{
		$this->target = $target;
	}

	/**
	 * Returns the base resource.
	 * @return \Light\ObjectService\Type\ResolvedValue
	 */
	final public function getBaseResource()
	{
		return $this->base;
	}

	/**
	 * Returns the target resource.
	 * @return \Light\ObjectService\Type\ResolvedValue
	 */
	final public function getTargetResource()
	{
		return $this->target;
	}
}