<?php

namespace Light\ObjectService\Service\Response;

use Light\ObjectService\Type\CollectionType;

/**
 * A projected representation of a collection resource. 
 *
 */
final class DataCollection implements DataEntity
{
	const CLASSNAME = __CLASS__;
	
	/** @var \Light\ObjectService\Type\CollectionType */
	private $type;
	/** @var mixed */
	private $data;
	
	public function __construct(CollectionType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * Returns the data in this collection as an array.
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * Sets the data in this collection.
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}
	
	/**
	 * Returns the type of data in this object.
	 * @return \Light\ObjectService\Type\CollectionType
	 */
	public function getType()
	{
		return $this->type;
	}
}
