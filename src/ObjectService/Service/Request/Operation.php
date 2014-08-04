<?php
namespace Light\ObjectService\Service\Request;

use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Model\ResolvedValue;
use Light\ObjectService\Expression\ParsedNestedPathExpression;
use Light\ObjectService\Expression\ParsedRootPathExpression;
use Light\ObjectService\Model\PathReader;
use Light\Exception\Exception;

/**
 * A base class for describing requested operations on resources.
 * 
 */
abstract class Operation
{
	/** @var \Light\ObjectService\Service\Request\Operation */
	private $parent;
	
	/** @var \Light\ObjectService\Expression\PathExpression */
	private $resourcePath;
	
	/** @var \Light\ObjectService\Model\ResolvedValue */
	private $resource;
	
	/**
	 * Sets the parent operation.
	 * @param Operation $parent
	 * @return \Light\ObjectService\Service\Request\Operation
	 */
	final public function setParent(Operation $parent)
	{
		$this->parent = $parent;
		return $this;
	}
	
	/**
	 * Sets the path to the resource that is the subject of this operation.
	 * @param PathExpression $path
	 * @return \Light\ObjectService\Service\Request\Operation
	 */
	final public function setResourcePath(PathExpression $path)
	{
		$this->resourcePath = $path;
		return $this;
	}

	/**
	 * Sets the resource that is the subject of this operation.
	 * This should be the resource identified by {@link getResourcePath()}.
	 * @param ResolvedValue $resource
	 */
	final public function setResource(ResolvedValue $resource)
	{
		$this->resource = $resource;
	}
	
	/**
	 * Returns the parent operation.
	 * @return \Light\ObjectService\Service\Request\Operation
	 */
	final public function getParent()
	{
		return $this->parent;
	}
	
	/**
	 * Returns the path to the resource that is the subject of this operation.
	 * @return \Light\ObjectService\Expression\PathExpression
	 */
	final public function getResourcePath()
	{
		return $this->resourcePath;
	}
	
	/**
	 * Returns the resource that is the subject of this operation.
	 * 
	 * The resource might only be available after the operation has been executed.
	 * 
	 * @return \Light\ObjectService\Model\ResolvedValue
	 */
	final public function getResource()
	{
		return $this->resource;
	}
	
	abstract public function execute(ExecutionParameters $params);
	
	// utility methods
	
	/**
	 * Reads a resource value for this operation.
	 * 
	 * The resource is read based on the resource path. If the resource path
	 * is relative, then the resource on the parent operation is used.
	 * 
	 * @param ExecutionParameters $params
	 * @throws Exception
	 * @return \Light\ObjectService\Model\ResolvedValue
	 */
	final protected function readResource(ExecutionParameters $params)
	{
		if ($this->resourcePath->getRelativeTo())
		{
			if ($this->getParent() && $this->getParent()->getResource())
			{
				$resource = $this->getParent()->getResource();
			}
			else
			{
				throw new Exception("Could not access parent operation resource");
			}
			
			$parsed = new ParsedNestedPathExpression($this->resourcePath, $resource);
		}
		else
		{
			$parsed = new ParsedRootPathExpression($this->resourcePath, $params->getObjectRegistry());
		}
		
		$reader = new PathReader($parsed, $params->getObjectRegistry());
		return $reader->read();
	}
}
