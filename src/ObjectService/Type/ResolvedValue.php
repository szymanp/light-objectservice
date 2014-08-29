<?php
namespace Light\ObjectService\Type;

use Light\ObjectService\Expression\ParsedPathExpression;

/**
 * A value read from a resource path.
 *
 */
final class ResolvedValue
{
	/** @var mixed */
	private $value;
	
	/** @var \Light\ObjectService\Type\Type */
	private $type;

	/**
	 * The path corresponding to this resolved value.
	 * @var \Light\ObjectService\Expression\ParsedPathExpression
	 */
	private $path;
	
	public function __construct(Type $type, ParsedPathExpression $path, $value)
	{
		$this->value = $value;
		$this->type	 = $type;
		$this->path  = $path;
	}

	/**
	 * Returns the value.
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Returns the type of the value.
	 * @return \Light\ObjectService\Type\Type
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * Returns the path that was read to obtain this value.
	 * @return \Light\ObjectService\Expression\ParsedPathExpression
	 */
	public function getPath()
	{
		return $this->path;
	}
}