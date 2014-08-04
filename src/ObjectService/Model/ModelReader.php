<?php

namespace Light\ObjectService\Model;

use Light\ObjectService\Exceptions\ResolutionException;
use Light\Data\Helper;
use Light\Exception\InvalidParameterType;
use Light\Exception\NotImplementedException;

class ModelReader
{
	/** @var \Light\ObjectService\Model\ComplexType */
	private $type;
	
	/** @var object */
	private $object;
	
	public function __construct(ComplexType $type, $object)
	{
		$this->type 	= $type;
		$this->object	= $object;
		
		if (is_scalar($object))
		{
			throw new InvalidParameterType('$object', $object, "object|array");
		}
	}
	
	/**
	 * Reads the value of a property.
	 * @param string	$propertyName
	 * @throws ResolutionException
	 * @return mixed	the property value.
	 */
	public function readProperty($propertyName)
	{
		$spec = $this->type->getSpecification();
		
		if (!$spec->canRead($propertyName))
		{
			throw new ResolutionException("Field %1::%2 is not readable", $this->type->getName(), $propertyName);
		} 
		
		$fieldSpec = $spec->getField($propertyName);
		if (is_null($fieldSpec))
		{
			// We don't have any specification for this field,
			// therefore we return the raw value from the object.
			try
			{
				return $this->readPropertyInternal($propertyName);
			}
			catch (\Exception $e)
			{
				throw new ResolutionException("Field %1::%2 cannot be read: %3", $this->type->getName(), $propertyName, $e->getMessage(), $e);
			}
		}
		elseif (!is_null($getter = $fieldSpec->getGetter()))
		{
			$value = call_user_func($getter, $this->object);
		}
		else
		{
			try
			{
				$value = $this->readPropertyInternal($fieldSpec->getPropertyName());
			}
			catch (\Exception $e)
			{
				throw new ResolutionException("Field %1::%2 cannot be read: %3", $this->type->getName(), $propertyName, $e->getMessage(), $e);
			}
		}
		
		return $value;
	}
	
	protected function readPropertyInternal($propertyName)
	{
		$wrapped = Helper::wrap($this->object);
		return $wrapped->getValue($propertyName);
	}
}