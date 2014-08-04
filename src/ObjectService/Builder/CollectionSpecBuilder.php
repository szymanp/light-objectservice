<?php

namespace Light\ObjectService\Builder;

use Light\ObjectService\Exceptions\TypeException;
use Light\Exception\Exception;

class CollectionSpecBuilder
{
	private $fields = array();
	
	/**
	 * Returns a field definition for the named field. 
	 * @param string $name
	 * @return \Light\ObjectService\Builder\RestrictionBuilder
	 */
	public function field($name)
	{
		if (!isset($this->fields[$name]))
		{
			return $this->fields[$name] = new RestrictionBuilder($this, $name);
		}
		return $this->fields[$name];
	}
	
	// getters
	
	/**
	 * Returns a field builder for this field, if it exists.
	 * @param string	$name
	 * @return \Light\ObjectService\Builder\RestrictionBuilder
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
	 * @return \Light\ObjectService\Builder\RestrictionBuilder
	 */
	public function getFieldOrThrow($name)
	{
		if (isset($this->fields[$name]))
		{
			return $this->fields[$name];
		}
		throw new TypeException("Field \"%1\" does not exist in specification", $name);
	}
	
	/**
	 * Returns the name of the specified field.
	 * @param RestrictionBuilder $field
	 * @return string
	 */
	public function getFieldName(RestrictionBuilder $field)
	{
		if ($name = array_search($field, $this->fields))
		{
			return $name;
		}
		throw new Exception("Field does not exist in specification");
	}
	
	/**
	 * Returns a list of all restriction fields.
	 * @return array<string>
	 */
	public function getAllFields()
	{
		$fields = array();
		foreach($this->fields as $name => $field)
		{
			$fields[] = $name;
		}
		return $fields;
	}
}
