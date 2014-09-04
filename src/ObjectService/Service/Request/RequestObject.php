<?php

namespace Light\ObjectService\Service\Request;

use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Expression\SelectExpressionSource;
use Light\ObjectService\Resource\Operation\Operation;

/**
 * An implementation of the {@link Request} interface.
 *
 */
final class RequestObject implements Request
{
	private $resourcePath;
	private $operation;
	private $selection;
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Request\Request::getResourcePath()
	 */
	public function getResourcePath()
	{
		return $this->resourcePath;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Request\Request::getOperation()
	 */
	public function getOperation()
	{
		return $this->operation;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Request\Request::getSelection()
	 */
	public function getSelection()
	{
		return $this->selection;
	}

	/**
	 * Sets the path to the requested resource.
	 * @param PathExpression $path
	 */
	public function setResourcePath(PathExpression $path)
	{
		$this->resourcePath = $path;
	}
	
	/**
	 * Sets the operation to be performed on the requested resource.
	 * @param Operation $operation
	 */
	public function setOperation(Operation $operation)
	{
		$this->operation = $operation;
	}
	
	/**
	 * Sets the expression for selecting fields of the requested resource.
	 * @param SelectExpressionSource $selection
	 */
	public function setSelection(SelectExpressionSource $selection)
	{
		$this->selection = $selection;
	}
}
