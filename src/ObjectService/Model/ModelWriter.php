<?php

namespace Light\ObjectService\Model;

use Light\ObjectService\Exceptions\ResolutionException;
use Light\Data\Helper;
use Light\Exception\InvalidParameterType;
use Light\ObjectService\Transaction\Transaction;

class ModelWriter
{
	/** @var \Light\ObjectService\Transaction\Transaction */
	private $transaction;
	
	/** @var \Light\ObjectService\Model\ComplexType */
	private $type;
	
	/** @var object */
	private $object;
	
	public function __construct(Transaction $tx, ComplexType $type, $object)
	{
		$this->transaction	= $tx;
		$this->type 		= $type;
		$this->object		= $object;
		
		if (!is_object($object))
		{
			throw new InvalidParameterType('$object', $object, "object");
		}
	}
	
	/**
	 * Sets the value of a property.
	 * @param string	$propertyName
	 * @param mixed		$value
	 * @throws ResolutionException
	 */
	public function setProperty($propertyName, $value)
	{
		$this->checkWritable($propertyName);
		$this->openTransaction();
		
		$fieldSpec = $this->type->getSpecification()->getField($propertyName);
		if (is_null($fieldSpec))
		{
			// We don't have any specification for this field,
			// therefore we try to write the raw value.
			try
			{
				return $this->writePropertyInternal($propertyName, $value);
			}
			catch (\Exception $e)
			{
				throw new ResolutionException("Field %1::%2 cannot be written: %3", $this->type->getName(), $propertyName, $e->getMessage(), $e);
			}
		}
		elseif (!is_null($setter = $fieldSpec->getSetter()))
		{
			call_user_func($setter, $this->object, $value, $this->transaction);
		}
		else
		{
			try
			{
				$value = $this->writePropertyInternal($fieldSpec->getPropertyName(), $value);
			}
			catch (\Exception $e)
			{
				throw new ResolutionException("Field %1::%2 cannot be written: %3", $this->type->getName(), $propertyName, $e->getMessage(), $e);
			}
		}
		
		$this->transaction->saveDirty($this->object);
	}
	
	public function appendToProperty($propertyName, $value)
	{
		$this->checkWritable($propertyName);
		$this->openTransaction();
		
		$this->transaction->saveDirty($this->object);
	}
	
	public function removeFromProperty($propertyName, $value)
	{
		$this->checkWritable($propertyName);
		$this->openTransaction();
		
		$this->transaction->saveDirty($this->object);
	}
	
	protected function writePropertyInternal($propertyName, $value)
	{
		$wrapped = Helper::wrap($this->object);
		$wrapped->setValue($propertyName, $value);
	}
	
	private function checkWritable($propertyName)
	{
		if (!$this->type->getSpecification()->canWrite($propertyName))
		{
			throw new ResolutionException("Field %1::%2 is not writable", $this->type->getName(), $propertyName);
		}
	}
	
	private function openTransaction()
	{
		if (!$this->transaction->isOpen())
		{
			$this->transaction->begin();
		}
		
	}
}