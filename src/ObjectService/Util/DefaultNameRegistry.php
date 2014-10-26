<?php
namespace Light\ObjectService\Util;

use Light\Exception\Exception;
use Light\ObjectService\NameRegistry;
use Light\ObjectService\Type\CollectionType;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Type\SimpleType;
use Light\ObjectService\Type\Type;
use Light\Util\URL;

class DefaultNameRegistry implements NameRegistry
{
	private $baseTypeUris		= array("" => "//");
	private $sortedTypeUris;
	
	private $uris = array();
	private $types = array();

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\NameRegistry::getTypeUri()
	 */
	public function getTypeUri(Type $type)
	{
		$hash = spl_object_hash($type);
		
		if (isset($this->uris[$hash]))
		{
			return $this->uris[$hash];
		}
		else
		{
			
			return $this->constructTypeUri($type);
		}
	}

	/**
	 * Assigns an URI to a type.
	 * @param Type $type
	 * @param string $uri
	 */
	public function setTypeUri(Type $type, $uri)
	{
		if (in_array($uri, $this->uris, true))
		{
			throw new Exception("URI <%1> is already assigned to a type", $uri);
		}
		
		$hash = spl_object_hash($type);
		$this->uris[$hash] = $uri;
		$this->types[$hash] = $type;
	}

	/**
	 * Sets the base URI for types.
	 * @param string $baseUri
	 */
	public function addTypeBaseUri($namespace, $baseUri)
	{
		$this->baseTypeUris[$namespace] = $baseUri;
		$this->sortedTypeUris = null;
	}

	protected final function getPrefix($name)
	{
		if (is_null($this->sortedTypeUris))
		{
			$this->sortedTypeUris = array_keys($this->baseTypeUris);
			usort($this->sortedTypeUris, function($a, $b)
			{
				return strlen($a) < strlen($b);
			});
		}
		
		foreach($this->sortedTypeUris as $prefix)
		{
			if (substr($name, 0, strlen($prefix)) == $prefix)
			{
				$result = new \stdClass();
				$result->baseUri = $this->baseTypeUris[$prefix];
				$result->name	 = substr($name, strlen($prefix));
				return $result;
			}
		}

		return null;
	}
	
	protected function constructTypeUri(Type $type)
	{
		if ($type instanceof ComplexType)
		{
			$prefix   = $this->getPrefix($type->getClassName());
			$suffix = "complex";
		}
		else if ($type instanceof CollectionType)
		{
			$suffix = "collection";
			$basetype = $type->getBaseType();
			if ($basetype instanceof ComplexType)
			{
				$prefix = $this->getPrefix($type->getBaseType()->getClassName());
			}
			else if ($basetype instanceof SimpleType)
			{
				$prefix = $this->getPrefix($type->getBaseType()->getPhpType());
			}
		}
		else if ($type instanceof SimpleType)
		{
			$prefix = $this->getPrefix($type->getPhpType());
			$suffix = "simple";
		}
		
		return URL::joinPaths(array($prefix->baseUri, strtr($prefix->name, "\\", "/"))) . "#" . $suffix;
	}
}