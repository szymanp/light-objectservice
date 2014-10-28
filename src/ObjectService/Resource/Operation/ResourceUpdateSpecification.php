<?php
namespace Light\ObjectService\Resource\Operation;

use Light\Exception\InvalidParameterType;
use Light\ObjectService\Exceptions\TypeException;
use Light\ObjectService\Resource\ResourceSpecification;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Resource\ResolvedValue;

final class ResourceUpdateSpecification
{
	/** @var array<string, mixed|ResourceSpecification> */
	private $values;

	/**
	 * Sets the value for a field.
	 * @param string	$fieldName
	 * @param mixed		$value
	 */
	public function setValue($fieldName, $value)
	{
		if (!is_scalar($value) && !is_array($value))
		{
			throw new InvalidParameterType('$value', $value, "Only scalar and array values are allowed");
		}
		$this->values[$fieldName] = $value;
	}

	/**
	 * Sets the value of a field to a resource.
	 * @param string				$fieldName
	 * @param ResourceSpecification $resourceSpecification
	 */
	public function setResource($fieldName, ResourceSpecification $resourceSpecification)
	{
		$this->values[$fieldName] = $resourceSpecification;
	}

	/**
	 * Updates the given resource with this field-value specification.
	 * @param \Light\ObjectService\Resource\ResolvedValue 		$resource
	 * @param ExecutionParameters 	$executionParameters
	 * @throws TypeException
	 * @throws \Light\ObjectService\Exceptions\ResolutionException
	 */
	public function update(ResolvedValue $resource, ExecutionParameters $executionParameters)
	{
		$type = $resource->getType();
		if (!($type instanceof ComplexType))
		{
			throw new TypeException("Only complex-type resources can be updated");
		}

		$object = $resource->getValue();

		foreach ($this->values as $fieldName => $value)
		{
			if ($value instanceof ResourceSpecification)
			{
				$result = $value->resolve($executionParameters);

				$type->writeProperty($object, $fieldName, $result->getValue(), $executionParameters->getTransaction());
			}
			else
			{
				$type->writeProperty($object, $fieldName, $value, $executionParameters->getTransaction());
			}
		}
	}
} 