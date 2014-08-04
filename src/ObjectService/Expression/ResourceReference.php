<?php 
namespace Light\ObjectService\Expression;

use Light\ObjectService\Model\ResolvedValue;

interface ResourceReference
{
	/**
	 * @return boolean|null
	 */
	public function isCollection();
	
	/**
	 * @return \Light\ObjectService\Model\ResolvedValue
	 */
	public function findResource();
}