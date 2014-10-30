<?php
namespace Light\ObjectService\Service\Request;

use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Expression\SelectExpressionSource;
use Light\ObjectService\Resource\Addressing\ResourceIdentifier;
use Light\ObjectService\Resource\Operation\Operation;
use Light\ObjectService\Resource\UrlResourceSpecification;

/**
 * An implementation of the {@link Request} interface.
 *
 */
final class RequestObject implements Request
{
	/** @var ResourceIdentifier */
	private $resourceIdentifier;
	/** @var Operation[] */
	private $operations = array();
	/** @var SelectExpressionSource */
	private $selection;

	/**
	 * @return Operation[]
	 */
	public function getOperations()
	{
		return $this->operations;
	}

	/**
	 * @param Operation $operations
	 */
	public function addOperation(Operation $operation)
	{
		$this->operations[] = $operation;
	}

	/**
	 * @return ResourceIdentifier
	 */
	public function getResourceIdentifier()
	{
		return $this->resourceIdentifier;
	}

	/**
	 * @param ResourceIdentifier $resourceIdentifier
	 */
	public function setResourceIdentifier($resourceIdentifier)
	{
		$this->resourceIdentifier = $resourceIdentifier;
	}

	/**
	 * @return SelectExpressionSource
	 */
	public function getSelection()
	{
		return $this->selection;
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
