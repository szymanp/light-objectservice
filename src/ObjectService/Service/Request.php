<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Selection\RootSelection;

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
	 * Returns the field selection to be applied to the requested resource.
	 * The selection returned by this method will be applied to the requested
	 * resource to return data in the response to this request.
	 * @return RootSelection
	 */
	public function getSelection();
}
