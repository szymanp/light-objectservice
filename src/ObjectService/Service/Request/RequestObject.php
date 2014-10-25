<?php
namespace Light\ObjectService\Service\Request;

use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Expression\SelectExpressionSource;
use Light\ObjectService\Resource\Operation\Operation;
use Light\ObjectService\Resource\UrlResourceSpecification;

/**
 * An implementation of the {@link Request} interface.
 *
 */
final class RequestObject implements Request
{
	private $resourceSpecification;
	private $selection;
	
	public function getResourceSpecification()
	{
		return $this->resourceSpecification;
	}

	public function getSelection()
	{
		return $this->selection;
	}

	/**
	 * Sets the specification of the requested resource.
	 * @param UrlResourceSpecification $resourceSpecification
	 */
	public function setResourceSpecification(UrlResourceSpecification $resourceSpecification)
	{
		$this->resourceSpecification = $resourceSpecification;
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
