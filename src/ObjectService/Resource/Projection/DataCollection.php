<?php
namespace Light\ObjectService\Resource\Projection;

use Light\ObjectAccess\Resource\Addressing\ResourceAddress;
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
	/** @var ResourceAddress */
	private $address;
	
	public function __construct(CollectionTypeHelper $typeHelper, ResourceAddress $address)
	{
		$this->typeHelper = $typeHelper;
		$this->address = $address;
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

	/**
	 * Returns the address associated with this entity.
	 * @return ResourceAddress
	 */
	public function getResourceAddress()
	{
		return $this->address;
	}
}
