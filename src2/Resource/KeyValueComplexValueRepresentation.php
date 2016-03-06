<?php
namespace Szyman\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedObject;
use Szyman\Exception\InvalidArgumentException;
use Szyman\ObjectService\Service\ComplexValueModification;
use Szyman\ObjectService\Service\ComplexValueRepresentation;
use Szyman\ObjectService\Service\ExecutionEnvironment;

/**
 * Stores a full or partial representation of a complex value in terms of fields and their values.
 */
class KeyValueComplexValueRepresentation implements ComplexValueRepresentation, ComplexValueModification
{
	/** @var array<string, mixed> */
	private $values = array();

	/**
	 * Sets the new value for a field.
	 * @param string	$fieldName
	 * @param mixed		$value
	 */
	public function setValueReplacement($fieldName, $value)
	{
		if (!is_scalar($value) && !is_array($value))
		{
			throw InvalidArgumentException::newInvalidType('$value', $value, "scalar");
		}
		$this->values[$fieldName] = $value;
	}

	/**
	 * Sets the new complex value of a field.
	 * @param string					 $fieldName
	 * @param ComplexValueRepresentation $value
	 */
	public function setComplexValueReplacement($fieldName, ComplexValueRepresentation $value)
	{
		$this->values[$fieldName] = $value;
	}

	// TODO setCollectionValueReplacement
	// TODO setCollectionValueUpdate

	/**
	 * Updates the complex value of a field.
	 * @param string                   $fieldName
	 * @param ComplexValueModification $valueUpdate
	 */
	public function setComplexValueUpdate($fieldName, ComplexValueModification $valueUpdate)
	{
		$this->values[$fieldName] = $valueUpdate;
	}

	/**
	 * Updates the value of a complex resource to the one contained in this object.
	 *
	 * @param ResolvedObject       $target The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 */
	public function updateObject(ResolvedObject $target, ExecutionEnvironment $environment)
	{
		// TODO: Implement updateObject() method.
	}

	/**
	 * Set the value of a complex resource to the one contained in this object.
	 *
	 * @param ResolvedObject       $target The resource to be updated.
	 * @param ExecutionEnvironment $environment
	 */
	public function replaceObject(ResolvedObject $target, ExecutionEnvironment $environment)
	{
		// TODO: Implement replaceObject() method.
	}
}