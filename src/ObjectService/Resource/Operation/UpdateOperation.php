<?php
namespace Light\ObjectService\Resource\Operation;

use Light\Exception\Exception;
use Light\Exception\InvalidParameterType;
use Light\ObjectService\Exceptions\TypeException;
use Light\ObjectService\Resource\FieldTransformation;
use Light\ObjectService\Resource\ResourceSpecification;
use Light\ObjectService\Type\ComplexType;

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
	 * @param string				$fieldName
	 * @param FieldTransformation 	$transformation
	 */
	public function setFieldTransformation($fieldName, FieldTransformation $transformation)
	{
		$this->fields[$fieldName] = $transformation;
	}
	
	/**
	 * Returns a list of field-value pairs.
	 * @return array<string, mixed>	The value can be either a simple value, an array, or an FieldTransformation object.
	 */
	public function getFields()
	{
		return $this->fields;
	}
	
	public function execute(ExecutionParameters $params)
	{
		if (!$this->getResource())
		{
			throw new Exception("A resource must be specified as a subject for this operation");
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
			else if ($value instanceof ResourceSpecification)
			{
				$result = $value->execute($params);

				$type->writeProperty($object, $fieldName, $result->getTargetResource(), $params->getTransaction());
			}
			else
			{
				$type->writeProperty($object, $fieldName, $value, $params->getTransaction());
			}
		}

		return $this->getResource();
	}
}