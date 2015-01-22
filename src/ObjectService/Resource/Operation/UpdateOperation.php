<?php
namespace Light\ObjectService\Resource\Operation;

use Light\Exception\InvalidParameterType;
use Light\ObjectAccess\Exception\ResourceException;
use Light\ObjectAccess\Exception\TypeException;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectService\Resource\ResourceSpecification;

final class UpdateOperation extends Operation
{
	/** @var array<string, mixed|ResourceSpecification> */
	private $values = array();

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
	 * Executes the update operation.
	 * @param ResolvedResource    $resource
	 * @param ExecutionParameters $parameters
	 * @throws ResourceException
	 * @throws TypeException
	 */
	public function execute(ResolvedResource $resource, ExecutionParameters $parameters)
	{
		if (!($resource instanceof ResolvedObject))
		{
			throw new TypeException("Only complex-type resources with concrete values can be updated");
		}

		$typeHelper = $resource->getTypeHelper();

		foreach ($this->values as $fieldName => $value)
		{
			if ($value instanceof ResourceSpecification)
			{
				$targetResource = $value->resolve($parameters);

				if ($targetResource instanceof ResolvedValue)
				{
					$value = $targetResource->getValue();
				}
				else
				{
					throw new ResourceException(
						"Resource \"%1\" does not have a value that can be assigned to property \"%2\" of \"%3\"",
						$targetResource->getAddress()->getAsString(),
						$fieldName,
						$resource->getAddress()->getAsString());
				}

				$typeHelper->writeProperty($resource, $fieldName, $value, $parameters->getTransaction());
			}
			else
			{
				$typeHelper->writeProperty($resource, $fieldName, $value, $parameters->getTransaction());
			}
		}
	}
} 