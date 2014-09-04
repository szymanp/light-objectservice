<?php

namespace Light\ObjectService\Type;

use Light\Exception\Exception;
use Light\ObjectService\ObjectRegistry;

/**
 * A base class for simple, complex and collection types.
 */
abstract class Type
{
	/** @var \Light\ObjectService\ObjectRegistry */
	private $objectRegistry;
	
	/**
	 * Attaches this type to the Object Registry.
	 * This method should not be called by user code.
	 * @param ObjectRegistry $registry
	 * @access protected
	 */
	public function attachToRegistry(ObjectRegistry $registry)
	{
		if (!is_null($this->objectRegistry))
		{
			throw new Exception("Type is already attached to an object registry");
		}
		$this->objectRegistry = $registry;
	}
	
	/**
	 * Returns the Object Registry that this type is attached to.
	 * @return \Light\ObjectService\ObjectRegistry
	 */
	public function getObjectRegistry()
	{
		return $this->objectRegistry;
	}
	
	/**
	 * Returns the URI for this type.
	 * @return string
	 */
	public function getUri()
	{
		return $this->objectRegistry->getNameRegistry()->getTypeUri($this);
	}
}