<?php
namespace Light\ObjectService\Service\Protocol;

use Light\ObjectService\Resource\Selection\RootSelection;

final class DeserializedResult
{
	/** @var Operation[] */
	private $operations;

	/** @var RootSelection */
	private $selection;

	/**
	 * @return Operation[]
	 */
	public function getOperations()
	{
		return $this->operations;
	}

	/**
	 * @param Operation[] $operations
	 */
	public function setOperations(array $operations)
	{
		$this->operations = $operations;
	}

	/**
	 * @return RootSelection
	 */
	public function getSelection()
	{
		return $this->selection;
	}

	/**
	 * @param RootSelection $selection
	 */
	public function setSelection(RootSelection $selection)
	{
		$this->selection = $selection;
	}
}