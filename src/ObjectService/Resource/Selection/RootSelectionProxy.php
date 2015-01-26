<?php
namespace Light\ObjectService\Resource\Selection;

use Light\Exception\Exception;
use Light\ObjectAccess\Type\ComplexTypeHelper;

/**
 * A proxy selection that can be used if the {@link ComplexTypeHelper} object is not available at a given time.
 */
abstract class RootSelectionProxy extends Selection
{
	/** @var Selection */
	private $innerSelection;

	/**
	 * Creates a root selection object for the given type helper.
	 * @param ComplexTypeHelper $typeHelper
	 * @return RootSelection
	 */
	abstract public function createSelection(ComplexTypeHelper $typeHelper);

	/**
	 * Prepares the selection proxy to be used with the given type.
	 * @param ComplexTypeHelper $typeHelper
	 * @throws Exception
	 */
	final public function prepare(ComplexTypeHelper $typeHelper)
	{
		if (is_null($this->innerSelection))
		{
			$this->innerSelection = $this->createSelection($typeHelper);
		}
		else if ($this->innerSelection->getTypeHelper() !== $typeHelper)
		{
			throw new Exception("The selection proxy was already prepared with a different type");
		}
	}

	/**
	 * Returns the prepared selection object.
	 * @return Selection
	 * @throws Exception
	 */
	final public function getInnerSelection()
	{
		if ($this->innerSelection)
		{
			return $this->innerSelection;
		}
		throw new Exception("The selection proxy has not been prepared yet");
	}

	/**
	 * @inheritdoc
	 */
	final public function getFields()
	{
		return $this->getInnerSelection()->getFields();
	}

	/**
	 * @inheritdoc
	 */
	final public function getSubSelection($fieldName)
	{
		return $this->getInnerSelection()->getSubSelection($fieldName);
	}

	/**
	 * @inheritdoc
	 */
	final public function getTypeHelper()
	{
		return $this->getInnerSelection()->getTypeHelper();
	}
}