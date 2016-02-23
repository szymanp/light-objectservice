<?php
namespace Light\ObjectService\Json\Request;

use Szyman\Exception\NotImplementedException;
use Light\ObjectAccess\Type\ComplexTypeHelper;
use Light\ObjectService\Resource\Selection\RootSelection;
use Light\ObjectService\Resource\Selection\RootSelectionProxy;

class JsonSelectionProxy extends RootSelectionProxy
{
	/** @var mixed */
	private $json;

	public function __construct($json)
	{
		$this->json = $json;
	}

	/**
	 * @param ComplexTypeHelper $typeHelper
	 * @return RootSelection
	 */
	public function createSelection(ComplexTypeHelper $typeHelper)
	{
		$selection = new RootSelection($typeHelper);
		$this->parseSelection($selection, $this->json);

		return $selection;
	}

	protected function parseSelection(RootSelection $target, $json)
	{
		if (is_string($json))
		{
			$target->fields($json);
		}
		else
		{
			// TODO
			throw new NotImplementedException();
		}
	}
}