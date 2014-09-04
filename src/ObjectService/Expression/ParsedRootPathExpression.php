<?php

namespace Light\ObjectService\Expression;

use Light\Exception\InvalidParameterValue;
use Light\ObjectService\Exceptions\ResolutionException;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Type\CollectionType;

/**
 * A ParsedPathExpression for a path that is not relative to any other path.
 *
 */
final class ParsedRootPathExpression extends ParsedPathExpression
{
	private $rootObjectName;
	private $rootObject;
	private $rootType;
		
	public function __construct(PathExpression $pathExpression, ObjectRegistry $registry)
	{
		if ($pathExpression->getRelativeTo())
		{
			throw new InvalidParameterValue('$pathExpression', $pathExpression, "An absolute PathExpression is required");
		}
		
		$this->copyFrom($pathExpression);
		
		$path = explode("/", $pathExpression->getPath());
		$result = $registry->findResource($path);
		
		if (is_null($result))
		{
			throw new ResolutionException("Could not resolve root model for path '%1'", $pathExpression->getPath());
		}
		
		$this->rootObjectName	= $result->name;
		if ($result->resource instanceof CollectionType)
		{
			$this->rootType 	= $result->resource;
		}
		else
		{
			$this->rootObject	= $result->resource;
			$this->rootType		= $registry->getType(get_class($this->rootObject));
		}
		
		$this->parsePathRemainder($result->remainder);
	}
	
		
	/**
	 * Returns the name of the root resource in the path.
	 * @return string
	 */
	public function getRootResourceName()
	{
		return $this->rootObjectName;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Expression\ParsedPathExpression::getRootType()
	 */
	public function getRootType()
	{
		return $this->rootType;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Expression\ParsedPathExpression::getRootObject()
	 */
	public function getRootObject()
	{
		return $this->rootObject;
	}
}
