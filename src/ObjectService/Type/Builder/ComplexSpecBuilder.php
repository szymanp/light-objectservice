<?php

namespace Light\ObjectService\Type\Builder;

use Light\ObjectService\Exceptions\TypeException;
use Light\Exception\Exception;

class ComplexSpecBuilder
{
	private $fields = array();
	
	/** @var FieldBuilder */
	private $keyField;
	
	/** @var string[] */
	private $excluded = array();
	
	/** @var string */
	private $classname;
	
	private $readAll = true;
	private $writeAll = true;

	/**
	 * Sets the class name of the objects described by this ComplexType.
	 * @param string $name
	 * @return \Light\ObjectService\Type\Builder\ComplexSpecBuilder
	 */
	public function classname($name)
	{
		$this->classname = $name;
		return $this;
	}

	/**
	 * Returns a field definition for the named field. 
	 * @param string $name
	 * @return \Light\ObjectService\Type\Builder\FieldBuilder
	 */
	public function field($name)
	{
		if (!isset($this->fields[$name]))
		{
			return $this->fields[$name] = new FieldBuilder($this, $name);
		}
		return $this->fields[$name];
	}
	
	/**
	 * Marks this field as inaccessible.
	 * @param string $name
	 * @return \Light\ObjectService\Type\Builder\ComplexSpecBuilder
	 */
	public function exclude($name)
	{
		if (!in_array($name, $this->excluded))
		{
			$this->excluded[] = $name;
		}
		return $this;
	}
	
	/**
	 * Marks the specified field as the key field.
	 * @param string|Field $nameOrField
	 * @return \Light\ObjectService\Type\Builder\ComplexSpecBuilder
	 */
	public function keyField($nameOrField)
	{
		if (is_string($name))
		{
			$this->keyField = $this->fields[$nameOrField];
		}
		else
		{
			$this->keyField = $nameOrField;
		}
		return $this;
	}
	
	// getters
	
	/**
	 * Returns the PHP class name of objects described by this ComplexType.
	 * @return string
	 */
	public function getClassname()
	{
		return $this->classname;
	}
	
	/**
	 * Returns a field builder for this field, if it exists.
	 * @param string	$name
	 * @return \Light\ObjectService\Type\Builder\FieldBuilder
	 */
	public function getField($name)
	{
		if (isset($this->fields[$name]))
		{
			return $this->fields[$name];
		}
	}
	
	/**
	 * Returns a field builder for this field.
	 * @param string	$name
	 * @return \Light\ObjectService\Type\Builder\FieldBuilder
	 */
	public function getFieldOrThrow($name)
	{
		if (isset($this->fields[$name]))
		{
			return $this->fields[$name];
		}
		throw new TypeException("Field \"%1\" does not exist in specification for class \"%2\"", $name, $this->classname);
	}
	
	/**
	 * Returns the name of the specified field.
	 * @param FieldBuilder $field
	 * @return string
	 */
	public function getFieldName(FieldBuilder $field)
	{
		if ($name = array_search($field, $this->fields))
		{
			return $name;
		}
		throw new Exception("Field does not exist in specification for class %1", $this->classname);
	}
	
	/**
	 * Retruns the key field for this model, if any.
	 * @return \Light\ObjectService\Type\Builder\FieldBuilder
	 */
	public function getKeyField()
	{
		return $this->keyField;
	}
	
	/**
	 * Returns a list of all readable fields.
	 * @return array<string>
	 */
	public function getAllFields()
	{
		$fields = array();
		foreach($this->fields as $name => $field)
		{
			if ($field->isReadable() && !in_array($name, $this->excluded, true))
			{
				$fields[] = $name;
			}
		}
		return $fields;
	}
	
	/**
	 * Returns true if this field can be read.
	 * @param string	$name
	 * @return boolean
	 */
	public function canRead($name)
	{
		if (in_array($name, $this->excluded))
		{
			return false;
		}
		else if (isset($this->fields[$name]))
		{
			return $this->fields[$name]->isReadable();
		}
		else
		{
			return $this->readAll;
		}
	}

	/**
	 * Returns true if this field can be changed.
	 * @param string	$name
	 * @return boolean
	 */
	public function canWrite($name)
	{
		if (in_array($name, $this->excluded))
		{
			return false;
		}
		else if (isset($this->fields[$name]))
		{
			return $this->fields[$name]->isWritable();
		}
		else
		{
			return $this->writeAll;
		}
	}
}
