<?php
namespace Light\ObjectService\Type\Util;

use Light\ObjectService\Type\ComplexTypeInterfaces\CreationContext;
use Light\ObjectService\Type\ComplexTypeInterfaces\DeletionContext;

class CreationDeletionContextObject implements CreationContext, DeletionContext
{
	/** @var object */
	private $contextObject;

	/**
	 * @param object $contextObject
	 */
	public function setContextObject($contextObject)
	{
		$this->contextObject = $contextObject;
	}

	/**
	 * @return object
	 */
	public function getContextObject()
	{
		return $this->contextObject;
	}

}