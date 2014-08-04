<?php

namespace Light\ObjectService\Service\Request;

use Light\ObjectService\Expression\PathExpression;

interface Request
{
	/**
	 * Returns the path to the requested resource.
	 * @return \Light\ObjectService\Expression\PathExpression
	 */
	function getResourcePath();
	
	/**
	 * Returns the select expression to be applied to the requested resource.
	 * The select expression returned by this method will be applied
	 * to the requested resource to return data in the response to this request.
	 * @return \Light\ObjectService\Expression\SelectExpressionSource
	 */
	function getSelection();
	
	/**
	 * Returns the operation to be performed on the requested resource.
	 * @return \Light\ObjectService\Service\Request\Operation
	 */
	function getOperation();
}
