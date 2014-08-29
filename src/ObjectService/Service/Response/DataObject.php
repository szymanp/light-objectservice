<?php

namespace Light\ObjectService\Service\Response;

use Light\ObjectService\Type\ComplexType;

/**
 * A projected representation of an object resource. 
 *
 */
final class DataObject implements DataEntity
{
	const CLASSNAME = __CLASS__;
	
	/** @var \Light\ObjectService\Type\ComplexType */
	private $type;
	/** @var \stdClass */
	private $data;
	
	public function __construct(ComplexType $type)
	{
		$this->type = $type;
		$this->data = new \stdClass;
	}
	
	/**
	 * Returns the data in this object as name-value pairs.
	 * @return \stdClass
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * Returns the type of data in this object.
	 * @return \Light\ObjectService\Type\ComplexType
	 */
	public function getType()
	{
		return $this->type;
	}
}
