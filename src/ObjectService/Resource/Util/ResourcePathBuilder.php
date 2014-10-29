<?php
namespace Light\ObjectService\Resource\Util;

use Light\Exception\Exception;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Resource\Addressing\ResourcePath;
use Light\ObjectService\Resource\Query\Scope;
use Light\ObjectService\Resource\ResolvedValue;

/**
 * A helper class for programatically building ResourcePath objects.
 */
final class ResourcePathBuilder
{
	/** @var ResolvedValue */
	private $sourceResource;
	/** @var ObjectRegistry */
	private $objectRegistry;
	/** @var array */
	private $elements = array();

	/**
	 * Creates a new ResourcePathBuilder from an existing resource.
	 * @param ResolvedValue $resource
	 * @return ResourcePathBuilder
	 */
	public static function createFromResource(ResolvedValue $resource)
	{
		$builder = new self();
		$builder->sourceResource = $resource;
		return $builder;
	}

	/**
	 * Creates a new ResourcePathBuilder for resolving an object from the ObjectRegistry.
	 * @param ObjectRegistry $registry
	 * @return ResourcePathBuilder
	 */
	public static function createFromRegistry(ObjectRegistry $registry)
	{
		$builder = new self();
		$builder->objectRegistry = $registry;
		return $builder;
	}

	private function __construct()
	{
		// Private constructor
	}

	/**
	 * Appends a resource path element to the path.
	 * @param string $path
	 * @return $this
	 */
	public function appendPath($path)
	{
		$count = count($this->elements);
		if ($count > 0 && is_string($this->elements[$count-1]))
		{
			$this->elements[$count-1] .= "/" . $path;
		}
		else
		{
			$this->elements[] = $path;
		}
		return $this;
	}

	/**
	 * Appends a Scope to the path.
	 * @param Scope $scope
	 * @return $this
	 */
	public function appendScope(Scope $scope)
	{
		$this->elements[] = $scope;
		return $this;
	}

	/**
	 * Builds a new ResourcePath object.
	 * @return ResourcePath
	 */
	public function build()
	{
		if (is_null($this->sourceResource))
		{
			$this->readResourceFromObjectRegistry();
		}

		$elements = array();
		foreach($this->elements as $element)
		{
			if (is_string($element))
			{
				$elements = array_merge($elements, explode("/", $element));
			}
			else
			{
				$elements[] = $element;
			}
		}

		return ResourcePath::create($this->sourceResource, $elements);
	}

	private function readResourceFromObjectRegistry()
	{
		if (count($this->elements) == 0)
		{
			throw new Exception("The path cannot be empty");
		}

		$path = $this->elements[0];

		if (!is_string($path))
		{
			throw new Exception("The initial element in the path must be a string");
		}

		// Find the resource matching the beginning of the path
		$result = $this->objectRegistry->findResource(explode("/", $path));
		if (is_null($result))
		{
			throw new ResolutionException("Could not resolve root resource for path \"%1\"", $path);
		}

		$this->sourceResource = $result->resource;
		$this->elements[0] = implode("/", $result->remainder);
		if ($this->elements[0] === "")
		{
			array_shift($this->elements);
		}
	}
}