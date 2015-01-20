<?php
namespace Light\ObjectService\Resource\Projection;

use Light\ObjectAccess\Type\CollectionTypeHelper;

/**
 * A projected representation of a collection resource. 
 *
 */
final class DataCollection implements DataEntity
{
	/** @var CollectionTypeHelper */
	private $typeHelper;
	/** @var mixed */
	private $data;
	
	public function __construct(CollectionTypeHelper $typeHelper)
	{
		$this->typeHelper = $typeHelper;
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
	 * @return CollectionTypeHelper
	 */
	public function getTypeHelper()
	{
		return $this->typeHelper;
	}
}
