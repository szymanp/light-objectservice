<?php
namespace Light\ObjectService\Resource\Util;

use Light\ObjectService\Resource\Selection\Selection;
use Light\ObjectService\Resource\Selection\SelectionSearchContext;

class DefaultSearchContext implements SelectionSearchContext
{
	/** @var Selection */
	private $selectionHint;

	/**
	 * Returns the field selection that will be used for projecting data from the objects.
	 *
	 * When reading data from a database it is useful to know what fields are needed
	 * to optimize the query. This information can be obtained from the selection object.
	 *
	 * @return Selection
	 */
	public function getSelectionHint()
	{
		return $this->selectionHint;
	}

	/**
	 * @param Selection $selectionHint
	 */
	public function setSelectionHint(Selection $selectionHint)
	{
		$this->selectionHint = $selectionHint;
	}
}