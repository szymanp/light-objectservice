<?php 

namespace Light\ObjectService\Model;

use Light\ObjectService\Builder\CollectionSpecBuilder;
use Light\Exception\Exception;

abstract class CollectionType extends Type
{
	/** @var \Light\ObjectService\Builder\CollectionSpecBuilder */
	private $spec;
		
	/** @var Type */
	private $baseType;
	
	protected function __construct(Type $baseType)
	{
		$this->baseType = $baseType;
		$this->spec = new CollectionSpecBuilder();
	}
	
	/**
	 * Returns a specification for this type.
	 * @return \Light\ObjectService\Builder\CollectionSpecBuilder
	 */
	final public function getSpecification()
	{
		return $this->spec;
	}
	
	/**
	 * Returns the type of items in this collection.
	 * @return \Light\ObjectService\Model\Type
	 */
	final public function getBaseType()
	{
		return $this->baseType;
	}
	
	/**
	 * Returns the first and only element of the specified collection.
	 * 
	 * This method will be called with the collection described by this type to read
	 * the only element of in this collection. If the collection is empty or has more
	 * than one element, the method should throw an exception. 
	 * 
	 * @param mixed	$value
	 * @throws Exception
	 * @return mixed
	 */
	public function getSingleElement($value)
	{
		if (is_array($value) || $value instanceof \Countable)
		{
			$count = count($value);
	
			if ($count == 0)
			{
				throw new Exception("Cannot read a single element of an empty collection");
			}
			elseif ($count > 1)
			{
				throw new Exception("Cannot read a single element of a multi-element collection");
			}
		}
			
		// Read the first element
		if (is_array($value))
		{
			return reset($value);
		}
		elseif ($value instanceof \Iterator)
		{
			$value->rewind();
			return $value->current();
		}
	
		throw new Exception("Cannot read first element of collection");
	}
}