<?php

namespace Light\ObjectService\Expression;

use Light\ObjectService\Exceptions\InvalidRequestException;

abstract class ParsedPathExpression extends PathExpression
{
	protected $pathElements = array();
	
	/**
	 * Returns the type of the root resource in the path.
	 * @eturn \Light\ObjectService\Model\Type
	 */
	abstract public function getRootType();
	
	/**
	 * Returns the object, if any, representing the root resource in the path. 
	 * @return object
	 */
	abstract public function getRootObject();
	
	/**
	 * Returns a list of elements in the property path.
	 * @return array
	 */
	public function getPathElements()
	{
		return $this->pathElements;
	}
	
	/**
	 * Parse the remainder of the path.
	 * @param array $path
	 * @throws InvalidRequestException
	 */
	protected function parsePathRemainder(array $path)
	{
		foreach($path as $element)
		{
			if (is_numeric($element))
			{
				// a numeric identifier
				$this->pathElements[] = (integer) $element;
			}
			elseif ($element[0] == "_")
			{
				// a where reference
				$ref = $this->getWhereReference($element);
				if (is_null($ref))
				{
					throw new InvalidRequestException("WHERE reference \"%1\" used in path \"%2\" is not defined", $element, $this->getPath());
				}
				$this->pathElements[] = $ref;
			}
			else
			{
				// a property name
				$this->pathElements[] = (string) $element;
			}
		}
	
		if (!is_null($ref = $this->getWhereReference(self::TARGET))
			&& end($this->pathElements) !== $ref)
		{
			$this->pathElements[] = $ref;
		}
	}
}
