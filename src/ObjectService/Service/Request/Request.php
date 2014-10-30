<?php
namespace Light\ObjectService\Service\Request;

use Light\ObjectService\Resource\Addressing\ResourceIdentifier;
use Light\ObjectService\Resource\Operation\Operation;

interface Request
{
	/**
	 * Returns the identifier of the requested resource.
	 * @return ResourceIdentifier
	 */
	function getResourceIdentifier();


	/**
	 * Returns a list of operations to be performed on the resource.
	 * @return Operation[]
	 */
	function getOperations();
	
	/**
	 * Returns the select expression to be applied to the requested resource.
	 * The select expression returned by this method will be applied
	 * to the requested resource to return data in the response to this request.
	 * @return \Light\ObjectService\Expression\SelectExpressionSource
	 */
	function getSelection();
}
