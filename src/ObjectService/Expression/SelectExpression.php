<?php

namespace Light\ObjectService\Expression;

use Light\ObjectService\Type\TypeHelper;
use Light\ObjectService\Type\SimpleType;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Type\CollectionType;
use Light\Exception\InvalidParameterType;
use Light\Exception\TypeException;

abstract class SelectExpression
{
	const ALL = "*";
	
	/** @var string[] */
	private $fields = array();
	
	/** @var array<string, NestedSelectExpression> */
	private $subselections = array();
	
	/** @var \Light\ObjectService\Type\ComplexType */
	private $type;
	
	/**
	 * Returns a new instance of a SelectExpression.
	 * 
	 * @return \Light\ObjectService\Expression\RootSelectExpression
	 */
	public static function create(ComplexType $type)
	{
		return new RootSelectExpression($type);
	}
	
	/**
	 * Constructs a new SelectExpression object.
	 * @param ComplexType $type
	 */
	public function __construct(ComplexType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * Add fields to this selection.
	 * @param string|array	$fields
	 * @throws InvalidParameterType
	 * @return \Light\ObjectService\Expression\SelectExpression
	 */
	final public function fields($fields)
	{
		$spec = $this->type->getSpecification();
		
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
	 * Returns an expression for selecting fields on a child object. 
	 * @param string $fieldName
	 * @return \Light\ObjectService\Expression\NestedSelectExpression
	 */
	final public function subselect($fieldName)
	{
		if (!in_array($fieldName, $this->fields))
		{
			$this->fields($fieldName);
		}
		if (!isset($this->subselections[$fieldName]))
		{
			$typeHelper = new TypeHelper($this->type->getObjectRegistry());
			$type = $typeHelper->getTypeForField($this->type->getSpecification()->getFieldOrThrow($fieldName));
			if ($type instanceof CollectionType)
			{
				$type = $type->getBaseType();
			}
			else if ($type instanceof SimpleType)
			{
				throw new TypeException("Field \"%1\" is of a simple type and cannot have a subselection", $fieldName);
			}
			
			$this->subselections[$fieldName] = new NestedSelectExpression($this, $type);
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
	 * Returns a subselection expression for the given field, if any.
	 * @param string $fieldName
	 * @return \Light\ObjectService\Expression\NestedSelectExpression
	 * 			A select expression, if defined; otherwise, NULL.
	 */
	final public function getSubselect($fieldName)
	{
		return @ $this->subselections[$fieldName];
	}
	
	/**
	 * Returns the type of object to which this selection is applicable.
	 * @return \Light\ObjectService\Type\ComplexType
	 */
	final public function getType()
	{
		return $this->type;
	}
	
	// TODO "join" method
	
	private function addField($fieldName)
	{
		if ($fieldName == self::ALL)
		{
			// Add all fields
			$allFields = $this->type->getSpecification()->getAllFields();
			$this->fields = array_unique(array_merge($this->fields, $allFields));
		}
		else if ($fieldName[0] == "-")
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
			$this->type->getSpecification()->getFieldOrThrow($fieldName);
			$this->fields[] = $fieldName;
		}
	}
}