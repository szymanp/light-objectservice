<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectService\Resource\FieldTransformation;
use Light\ObjectService\Type\ResolvedValue;

/**
 * A base class for describing requested operations on resources.
 * 
 */
abstract class Operation implements FieldTransformation
{
	/** @var \Light\ObjectService\Type\ResolvedValue */
	private $resource;
	
	/**
	 * Sets the resource that is the subject of this operation.
	 * @param ResolvedValue $resource
	 */
	final public function setResource(ResolvedValue $resource)
	{
		$this->resource = $resource;
	}
	
	/**
	 * Returns the resource that is the subject of this operation.
	 * 
	 * Note that some operations might not have any subject resource.
	 * 
	 * @return \Light\ObjectService\Type\ResolvedValue
	 */
	final public function getResource()
	{
		return $this->resource;
	}

	/**
	 * Executes the operation.
	 * @param ExecutionParameters $params
	 * @return ResolvedValue Result resource
	 */
	abstract public function execute(ExecutionParameters $params);
}
