<?php
namespace Light\ObjectService\Service\Request;

interface Request
{
	/**
	 * Returns the specification of the requested resource.
	 * @return \Light\ObjectService\Resource\UrlResourceSpecification
	 */
	function getResourceSpecification();
	
	/**
	 * Returns the select expression to be applied to the requested resource.
	 * The select expression returned by this method will be applied
	 * to the requested resource to return data in the response to this request.
	 * @return \Light\ObjectService\Expression\SelectExpressionSource
	 */
	function getSelection();
}
