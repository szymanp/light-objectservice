<?php 

namespace Light\ObjectService\Expression;

class Criterion extends Value
{
	const EQ	= "=";
	const GT	= ">";
	const LT	= "<";
	const LIKE	= "LIKE";
	
	private $operator;
	
	public function __construct($rawValue, $operator = self::EQ)
	{
		parent::__construct($rawValue);
		$this->operator = $operator;
	}
	
	public function getOperator()
	{
		return $this->operator;
	}
}