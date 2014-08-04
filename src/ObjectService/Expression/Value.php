<?php 

namespace Light\ObjectService\Expression;

class Value
{
	private $rawValue;
	private $value;
	
	public function __construct($rawValue)
	{
		$this->value = $this->rawValue = $rawValue;
	}
	
	public function getRawValue()
	{
		return $this->rawValue;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function setValue($value)
	{
		$this->value = $value;
	}
}