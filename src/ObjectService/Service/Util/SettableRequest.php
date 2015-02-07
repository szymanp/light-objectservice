<?php
namespace Light\ObjectService\Service\Util;

use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Operation\Operation;
use Light\ObjectService\Resource\Selection\RootSelection;
use Light\ObjectService\Service\Request;

/**
 * A Request implementation with settable properties.
 */
class SettableRequest implements Request
{
	/** @var EndpointRelativeAddress */
	private $resourceAddress;
	/** @var Operation[] */
	private $operations = array();
	/** @var RootSelection */
	private $selection;

	/**
	 * @param RootSelection $selection
	 */
	public function setSelection($selection)
	{
		$this->selection = $selection;
	}

	/**
	 * @param EndpointRelativeAddress $resourceAddress
	 */
	public function setResourceAddress(EndpointRelativeAddress $resourceAddress)
	{
		$this->resourceAddress = $resourceAddress;
	}

	/**
	 * @param Operation $operation
	 */
	public function addOperation(Operation $operation)
	{
		$this->operations[] = $operation;
	}

	/**
	 * @inheritdoc
	 */
	public function getResourceAddress()
	{
		return $this->resourceAddress;
	}

	/**
	 * @inheritdoc
	 */
	public function getOperations()
	{
		return $this->operations;
	}

	/**
	 * @inheritdoc
	 */
	public function getSelection()
	{
		return $this->selection;
	}
}