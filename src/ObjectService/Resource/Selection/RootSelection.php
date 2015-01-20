<?php
namespace Light\ObjectService\Resource\Selection;

use Light\Exception\InvalidParameterType;
use Light\ObjectAccess\Exception\TypeException;
use Light\ObjectAccess\Type\CollectionTypeHelper;
use Light\ObjectAccess\Type\ComplexTypeHelper;
use Light\ObjectAccess\Type\SimpleTypeHelper;

class RootSelection extends Selection
{
	const ALL = "*";
	const REMOVE = "-";
	
	/** @var string[] */
	private $fields = array();
	
	/** @var array<string, NestedSelection> */
	private $subselections = array();
	
	/** @var ComplexTypeHelper */
	private $typeHelper;
	
	/**
	 * Constructs a new RootSelection.
	 * @param ComplexTypeHelper $typeHelper
	 */
	public function __construct(ComplexTypeHelper $typeHelper)
	{
		$this->typeHelper = $typeHelper;
	}
	
	/**
	 * Add fields to this selection.
	 * @param string|array	$fields
	 * @throws InvalidParameterType
	 * @return $this
	 */
	final public function fields($fields)
	{
		if (is_string($fields))
		{
			$fields = explode(",", $fields);
			foreach($fields as $field)
			{
				$field = trim($field);
				$this->addField($field);
			}
		}
		elseif (is_array($fields))
		{
			foreach($fields as $field)
			{
				$this->addField($field);
			}
		}
		else
		{
			throw new InvalidParameterType('$fields', $fields, "string|array");
		}
		
		return $this;
	}

	/**
	 * Returns a selection for selecting fields on a child object.
	 * @param string $fieldName
	 * @return NestedSelection
	 * @throws TypeException
	 */
	final public function subselect($fieldName)
	{
		// Make sure that the field itself is included in the selection
		if (!in_array($fieldName, $this->fields))
		{
			$this->fields($fieldName);
		}

		if (!isset($this->subselections[$fieldName]))
		{
			$typeHelper = $this->typeHelper->getPropertyTypeHelper($fieldName);
			if ($typeHelper instanceof CollectionTypeHelper)
			{
				$typeHelper = $typeHelper->getBaseTypeHelper();
			}
			if ($typeHelper instanceof SimpleTypeHelper)
			{
				throw new TypeException("Field \"%1\" is of a simple type and cannot have a subselection", $fieldName);
			}
			
			$this->subselections[$fieldName] = new NestedSelection($this, $typeHelper);
		}
		return $this->subselections[$fieldName];
	}
	
	/**
	 * Returns a list of fields to be selected.
	 * @return string[]
	 */
	final public function getFields()
	{
		return $this->fields;
	}

	/**
	 * Returns a selection for a dependent object accessible via a named field.
	 * @param string $fieldName
	 * @return NestedSelection	A selection object, if defined for the field; otherwise, NULL.
	 */
	final public function getSubSelection($fieldName)
	{
		return @ $this->subselections[$fieldName];
	}

	/**
	 * Returns the type helper for the type that this selection is applicable to.
	 * @return ComplexTypeHelper
	 */
	final public function getTypeHelper()
	{
		return $this->typeHelper;
	}

	private function addField($fieldName)
	{
		// TODO

		if ($fieldName == self::ALL)
		{
			// Add all fields
			$allProperties = $this->typeHelper->getType()->getProperties();
			$names = array();
			foreach($allProperties as $property)
			{
				$names[] = $property->getName();
			}

			$this->fields = array_unique(array_merge($this->fields, $names));
		}
		else if ($fieldName[0] == self::REMOVE)
		{
			// Remove a field
			$fieldName = substr($fieldName, 1);
			$index = array_search($fieldName, $this->fields);
			if ($index !== false)
			{
				array_splice($this->fields, $index, 1);
			}
		}
		else if (!in_array($fieldName, $this->fields, true))
		{
			// Add a field

			// This will throw an exception if property doesn't exist
			$this->typeHelper->getPropertyTypeHelper($fieldName);
			$this->fields[] = $fieldName;
		}
	}
}