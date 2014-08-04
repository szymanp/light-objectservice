<?php 
namespace Light\ObjectService\Expression;

use Light\ObjectService\Model\ComplexType;

interface SelectExpressionSource
{
	/**
	 * Compiles the source select expression into a SelectExpression object. 
	 * @param ComplexType $type
	 * @return \Light\ObjectService\Expression\SelectExpression
	 */
	public function compile(ComplexType $type);
}