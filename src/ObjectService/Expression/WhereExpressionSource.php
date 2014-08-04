<?php 
namespace Light\ObjectService\Expression;

use Light\ObjectService\Model\CollectionType;

/**
 * An interface for objects that can be compiled into a WhereExpression.
 */
interface WhereExpressionSource
{
	/**
	 * Compiles the source WHERE expression into a WhereExpression object. 
	 * @param CollectionType $type
	 * @throws \Light\ObjectService\Exceptions\InvalidRequestException
	 * 								If the source data is malformed and cannot be parsed.
	 * 								This exception should be used if none of the other exceptions are applicable.
	 * @throws \Light\ObjectService\Exceptions\TypeException
	 * 								If the there is a problem with the type of one of the values, e.g.
	 * 								a value does not match a type, or the given type is not applicable in some context.
	 * 	
	 * @return \Light\ObjectService\Expression\WhereExpression
	 */
	public function compile(CollectionType $type);
}