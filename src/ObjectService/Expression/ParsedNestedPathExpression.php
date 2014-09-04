<?php

namespace Light\ObjectService\Expression;

use Light\Exception\Exception;
use Light\Exception\InvalidParameterValue;
use Light\ObjectService\Type\CollectionType;
use Light\ObjectService\Type\ResolvedValue;

/**
 * A ParsedPathExpression for a path that is relative to another path.
 *
 */
final class ParsedNestedPathExpression extends ParsedPathExpression
{
	/** @var \Light\ObjectService\Type\ResolvedValue */
	private $resolvedValue;
		
	public function __construct(PathExpression $pathExpression, ResolvedValue $value)
	{
		if (!$pathExpression->getRelativeTo())
		{
			throw new InvalidParameterValue('$pathExpression', $pathExpression, "A relative PathExpression is required");
		}
		
		if ($value->getPath()->getPath() != $pathExpression->getRelativeTo()->getPath())
		{
			throw new Exception("The relative-to path of the expression to be parsed is different than of the resolved value (\"%1\" vs \"%2\")",
								$pathExpression->getRelativeTo()->getPath(),
								$value->getPath()->getPath());
		}
		
		$this->copyFrom($pathExpression);
		$this->resolvedValue = $value;
		$path = explode("/", $pathExpression->getPath());
		
		$this->parsePathRemainder($path);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Expression\ParsedPathExpression::getRootType()
	 */
	public function getRootType()
	{
		return $this->resolvedValue->getType();
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Expression\ParsedPathExpression::getRootObject()
	 */
	public function getRootObject()
	{
		return $this->resolvedValue->getValue();
	}
}
