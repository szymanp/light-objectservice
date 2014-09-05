<?php
namespace Light\ObjectService\Type\Util;

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