<?php
namespace Light\ObjectService\Resource\Projection;
use Light\ObjectAccess\Type\ComplexTypeHelper;

/**
 * A projected representation of an object resource. 
 *
 */
final class DataObject implements DataEntity
{
	/** @var ComplexTypeHelper */
	private $typeHelper;
	/** @var \stdClass */
	private $data;
	
	public function __construct(ComplexTypeHelper $typeHelper)
	{
		$this->typeHelper = $typeHelper;
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
	 * Returns the type helper for the data in this object.
	 * @return ComplexTypeHelper
	 */
	public function getTypeHelper()
	{
		return $this->typeHelper;
	}
}
