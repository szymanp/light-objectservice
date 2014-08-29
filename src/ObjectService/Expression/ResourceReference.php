<?php 
namespace Light\ObjectService\Expression;

use Light\ObjectService\Type\ResolvedValue;

interface ResourceReference
{
	/**
	 * @return boolean|null
	 */
	public function isCollection();
	
	/**
	 * @return \Light\ObjectService\Type\ResolvedValue
	 */
	public function findResource();
}