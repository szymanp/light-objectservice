<?php 
namespace Light\ObjectService;

use Light\ObjectService\Type\Type;
use Light\ObjectService\Type\SimpleType;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Type\CollectionType;
use Light\ObjectService\Type\ObjectProvider;
use Light\ObjectService\Type\BuiltinType;
use Light\ObjectService\Type\BuiltinCollectionType;
use Light\ObjectService\Util\DefaultNameRegistry;
use Light\Exception\Exception;
use Light\Exception\InvalidParameterType;
use Light\Exception\NotImplementedException;

class ObjectRegistry
{
	const PATH_SEPARATOR = "/";
	
	/** @var array<string, object|ObjectProvider> */
	private $published = array();
	
	/** @var array<string, Type> */
	private $types = array();
	
	/** @var array<string, CollectionType> */
	private $collectionTypes = array();
	
	/** @var NameRegistry */
	private $nameRegistry;
	
	public function __construct()
	{
		$this->nameRegistry = new DefaultNameRegistry();
	}
	
	public function publishCollection($address, ObjectProvider $collection)
	{
		$this->published[$address] = $collection;
		$this->addType($collection);
		return $this;
	}
	
	public function publishObject($address, $object, ComplexType $type = null)
	{
		if (!is_object($object))
		{
			throw new InvalidParameterType('$object', $object, "object");
		}
		
		$this->published[$address] = $object;
		
		if (is_null($type))
		{
			if (!isset($this->types[get_class($object)]))
			{
				throw new Exception("No type for class \"%1\" has been registered", get_class($object));
			}
		}
		else
		{
			$this->addType($type);
		}
		return $this;
	}

	/**
	 * Registers a type.
	 * @param Type $type
	 * @return \Light\ObjectService\ObjectRegistry
	 */
	public function addType(Type $type)
	{
		if ($type instanceof SimpleType)
		{
			$this->types[$type->getPhpType()] = $type;
		}
		else if ($type instanceof ComplexType)
		{
			$this->types[$type->getClassName()] = $type;
		}
		else if ($type instanceof CollectionType)
		{
			$this->addCollectionType($type);
		}
		else
		{
			throw new InvalidParameterType('$type', $type, "SimpleType|ComplexType|CollectionType");
		}
		$type->attachToRegistry($this);
		
		return $this;
	}
	
	/**
	 * Registers a collection type.
	 * The base type is registered as well.
	 * @param CollectionType $type
	 * @return \Light\ObjectService\ObjectRegistry
	 */
	private function addCollectionType(CollectionType $type)
	{
		$baseType = $type->getBaseType();
		if ($baseType instanceof ComplexType)
		{
			$this->collectionTypes[$baseType->getClassName()] = $type;
			$this->addType($baseType);
		}
		else
		{
			throw new Exception("Currently only providers of complex types are supported");
		}
		return $this;
	}
	
	/**
	 * Returns a published resource.
	 * @param string	$address
	 * @return object|\Light\ObjectService\Type\ObjectProvider
	 */
	public function getResource($address)
	{
		return @ $this->published[$address];
	}
	
	/**
	 * Returns a published resource as an object provider.
	 * @throws Exception If the specified address doesn't resolve to an Object Provider. 
	 * @param string	$address
	 */
	public function getProvider($address)
	{
		$p = $this->getResource($address);
		if ($p instanceof ObjectProvider)
		{
			return $p;
		}
		else
		{
			throw new Exception("Address \"%1\" does not correspond to an object provider");
		}
	}
	
	/**
	 * Returns a type description for a PHP type.
	 * @param string $name
	 * @return \Light\ObjectService\Type\Type
	 */
	public function getType($name)
	{
		if (BuiltinType::isBuiltinType($name))
		{
			$type = $this->types[$name] = new BuiltinType(strtolower($name));
			$type->attachToRegistry($this);
			return $type;
		}
		
		return $this->types[$name];
	}
	
	/**
	 * Returns a collection type for a PHP type.
	 * @param string $baseTypeName
	 * @return \Light\ObjectService\Type\BuiltinCollectionType
	 */
	public function getCollectionType($baseTypeName)
	{
		if (isset($this->collectionTypes[$baseTypeName]))
		{
			return $this->collectionTypes[$baseTypeName];
		}
		else 
		{
			$type = $this->getType($baseTypeName);
			if (!$type)
			{
				throw new Exception("No type registered for \"%1\"", $baseTypeName);
			}
			$type = $this->collectionTypes[$baseTypeName] = new BuiltinCollectionType($type);
			$type->attachToRegistry($this);
			return $type;
		}
	}
	
	/**
	 * Sets a name registry to use.
	 * @param NameRegistry $nameRegistry
	 */
	public function setNameRegistry(NameRegistry $nameRegistry)
	{
		$this->nameRegistry = $nameRegistry;
	}
	
	/**
	 * Returns the name registry.
	 * @return \Light\ObjectService\NameRegistry
	 */
	public function getNameRegistry()
	{
		return $this->nameRegistry;
	}
	
	/**
	 * Returns a resource matching the beginning of the path.
	 * 
	 * For example, if a resource 'models/post' is published, then it would be found
	 * if a path like ["models", "post", "12", "title"] is specified.
	 * 
	 * @param array $path
	 * @return \stdClass|NULL
	 */
	public function findResource(array $path)
	{
		$remainder = $path;
		$str = "";
		foreach($path as $elem)
		{
			array_shift($remainder);
			
			if (!empty($str)) $str .= self::PATH_SEPARATOR;
			$str .= $elem;
			if (isset($this->published[$str]))
			{
				$result = new \stdClass();
				$result->name		= $str;
				$result->resource 	= $this->published[$str];
				$result->remainder	= $remainder;

				return $result;
			}
		}
		return null;
	}
}