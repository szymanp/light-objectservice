<?php
namespace Light\ObjectService\Formats\Uri;

use Light\ObjectAccess\Type\ComplexTypeHelper;
use Light\ObjectService\Resource\Selection\RootSelection;
use Light\ObjectService\Resource\Selection\RootSelectionProxy;

/**
 * Parses a field selection stored in a URL query string.
 */
class UriSelectionProxy extends RootSelectionProxy
{
	/** @var string */
	private $selectionString;

	public function __construct($selectionString)
	{
		$this->selectionString = $selectionString;
	}

	/**
	 * @inheritdoc
	 */
	public function createSelection(ComplexTypeHelper $typeHelper)
	{
		$selection = new RootSelection($typeHelper);

		$selection->fields($this->selectionString);

		return $selection;
	}
}