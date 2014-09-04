<?php 
namespace Light\ObjectService\Expression;

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