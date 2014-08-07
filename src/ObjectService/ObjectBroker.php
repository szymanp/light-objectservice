<?php 

namespace Light\ObjectService;

use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Model\ObjectProvider;
use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Expression\ParsedPathExpression;
use Light\ObjectService\Model\ComplexType;
use Light\ObjectService\Model\PathReader;

class ObjectBroker
{
	/** @var ObjectRegistry */
	private $objectRegistry;
	
	public function __construct()
	{
		$this->objectRegistry = new ObjectRegistry();
	}
	
	/**
	 * Returns the object registry.
	 * @return \Light\ObjectService\ObjectRegistry
	 */
	public function getRegistry()
	{
		return $this->objectRegistry;
	}
	
	public function getValue(PathExpression $path)
	{
		if ($path instanceof ParsedPathExpression)
		{
			$parsedPath = $path;
		}
		else
		{
			$parsedPath = new ParsedPathExpression($path, $this->objectRegistry);
		}
		
		$pathReader = new PathReader($parsedPath, $this->objectRegistry);
		return $pathReader->read();
	}

}