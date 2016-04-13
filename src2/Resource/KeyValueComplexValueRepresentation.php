<?php
namespace Szyman\ObjectService\Resource;

use Light\ObjectAccess\Exception\TypeException;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedValue;
use Szyman\Exception\InvalidArgumentException;
use Szyman\Exception\NotImplementedException;
use Szyman\ObjectService\Service\ComplexValueModification;
use Szyman\ObjectService\Service\ComplexValueRepresentation;
use Szyman\ObjectService\Service\ExecutionEnvironment;

/**
 * Stores a full or partial representation of a complex value in terms of fields and their values.
 */
final class KeyValueComplexValueRepresentation implements ComplexValueRepresentation, ComplexValueModification
{
	/** @var array<string, mixed> */
	private $values = array();

	/**
	 * Assigns a simple value to a field.
	 * The named field will be set to the given value.
	 * @param string	$fieldName
	 * @param mixed		$value
	 */
	public function setValue($fieldName, $value)
	{
		if (!is_scalar($value))
		{
			throw InvalidArgumentException::newInvalidType('$value', $value, "scalar");
		}
		$this->values[$fieldName] = $value;
	}
	
	/**
	 * Assigns a list of values to a field.
	 * The named field must represent a collection.
	 * The collection will be cleared and the specified values will be added to it.
	 * @param string	$fieldName
	 * @param mixed[]	$values		Each element in the collection must be a simple value, an array
	 *								or a {@link ResourceReference}.
	 */
	public function setArray($fieldName, array $values)
	{
		$this->values[$fieldName] = $values;
	}

	/**
	 * Assigns an object to a field.
	 * The named field will be set to object resolved from the reference.
	 * @param string            $fieldName
	 * @param ResourceReference $ref
	 */
	public function setResource($fieldName, ResourceReference $ref)
	{
		$this->values[$fieldName] = $ref;
	}

	/**
	 * Updates the value of a complex resource to the one contained in this object.
	 * @param ResolvedObject       $target The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 */
	public function updateObject(ResolvedObject $target, ExecutionEnvironment $environment)
	{
		try
		{
			$this->updateFields($target, $environment);
		}
		catch (TypeException $e)
		{
			throw new RepresentationTransferException(
				'Cannot update resource "%1": %2',
				$target->getAddress()->getAsString(),
				$e->getMessage(),
				$e);
		}
	}

	/**
	 * Set the value of a complex resource to the one contained in this object.
	 * @param ResolvedObject       $target The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 */
	public function replaceObject(ResolvedObject $target, ExecutionEnvironment $environment)
	{
		// TODO: Implement replaceObject() method.
	}

	/**
	 * @param ResolvedObject       $target
	 * @param ExecutionEnvironment $environment
	 * @throws NotImplementedException
	 * @throws \Light\ObjectAccess\Exception\TypeException
	 */
	private function updateFields(ResolvedObject $target, ExecutionEnvironment $environment)
	{
		$typeHelper = $target->getTypeHelper();

		foreach ($this->values as $fieldName => $fieldValue)
		{
			if ($fieldValue instanceof ResourceReference)
			{
				$valueResource = $fieldValue->resolve($environment);

				if ($valueResource instanceof ResolvedValue)
				{
					$value = $valueResource->getValue();
				}
				else
				{
					throw new RepresentationTransferException(
						'Resource "%1" cannot be assigned to property "%2" of resource "%3"',
						$valueResource->getAddress()->getAsString(),
						$fieldName,
						$target->getAddress()->getAsString());
				}

				$typeHelper->writeProperty($target, $fieldName, $value, $environment->getTransaction());
			}
			elseif (is_array($fieldValue))
			{
				throw new NotImplementedException;	// TODO
			}
			else
			{
				$typeHelper->writeProperty($target, $fieldName, $fieldValue, $environment->getTransaction());
			}
		}
	}
}
