<?php

namespace Light\ObjectService\Expression;

/**
 * An implementation of the FindContext interface.
 */
class FindContextObject implements FindContext
{
	/** @var object */
	private $contextObject;
	
	/** @var \Light\ObjectService\Expression\SelectExpression */
	private $selectExpression;
	
	/**
	 * Sets the related object for the query.
	 * @param object $o
	 */
	public function setContextObject($o)
	{
		$this->contextObject = $o;
	}
	
	/**
	 * Sets the selection hint.
	 * @param SelectExpression $hint
	 */
	public function setSelectionHint(SelectExpression $hint)
	{
		$this->selectExpression = $hint;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Expression\FindContext::getContextObject()
	 */
	public function getContextObject()
	{
		return $this->contextObject;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Expression\FindContext::getSelectionHint()
	 */
	public function getSelectionHint()
	{
		return $this->selectExpression;
	}
}