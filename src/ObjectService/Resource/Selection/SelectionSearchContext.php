<?php
namespace Light\ObjectService\Resource\Selection;

use Light\ObjectAccess\Type\Collection\SearchContext;

interface SelectionSearchContext extends SearchContext
{
	/**
	 * Returns the field selection that will be used for projecting data from the objects.
	 *
	 * When reading data from a database it is useful to know what fields are needed
	 * to optimize the query. This information can be obtained from the selection object.
	 *
	 * @return Selection
	 */
	public function getSelectionHint();
}