<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;

interface Request
{
	/**
	 * Returns the address of the resource that is the subject of this request.
	 * @return EndpointRelativeAddress
	 */
	public function getResourceAddress();

	// TODO

	/**
	 * Returns a list of operations to be performed on the resource.
	 * @return Operation[]
	 */
	public function getOperations();
	
	/**
	 * Returns the select expression to be applied to the requested resource.
	 * The select expression returned by this method will be applied
	 * to the requested resource to return data in the response to this request.
	 * @return \Light\ObjectService\Expression\SelectExpressionSource
	 */
	public function getSelection();
}
