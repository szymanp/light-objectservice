<?php
namespace Light\ObjectService\Service\Request;

use Light\Exception\InvalidParameterType;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Exceptions\TypeException;

class UpdateOperation extends Operation
{
	/**
	 * @var array<string, mixed>
	 */
	private $fields = array();
	
	/**
	 * Sets the value for a field.
	 * @param string	$fieldName
	 * @param mixed		$value
	 */
	public function setFieldValue($fieldName, $value)
	{
		if (!is_scalar($value) && !is_array($value))
		{
			throw new InvalidParameterType('$value', $value, "Only scalar and array values are allowed");
		}
		$this->fields[$fieldName] = $value;
	}
	
	/**
	 * Sets the operation for a field.
	 * @param string	$fieldName
	 * @param Operation $operation
	 */
	public function setFieldOperation($fieldName, Operation $operation)
	{
		$this->fields[$fieldName] = $operation;
	}
	
	/**
	 * Returns a list of field-value pairs.
	 * @return array<string, mixed>	The value can be either a simple value, an array, or an Operation object.
	 */
	public function getFields()
	{
		return $this->fields;
	}
	
	public function execute(ExecutionParameters $params)
	{
		if (!$this->getResource())
		{
			$this->setResource($this->readResource($params));
		}
		
		$type = $this->getResource()->getType();
		if (!($type instanceof ComplexType))
		{
			throw new TypeException("Only complex-type resources can be updated");
		}
		
		$object = $this->getResource()->getValue();
		
		foreach($this->fields as $fieldName => $value)
		{
			if ($value instanceof Operation)
			{
				$value->execute($params);
			}
			else
			{
				$type->writeProperty($object, $fieldName, $value, $params->getTransaction());
			}
		}
	}
}