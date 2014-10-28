<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\Exception\InvalidParameterValue;
use Light\ObjectService\Resource\Query\Scope;
use Light\ObjectService\Resource\Query\UrlScopeParser;
use Light\ObjectService\Resource\ResolvedValue;

/**
 * Provides a path from a source resource to a destination resource.
 */
final class ResourcePath
{
	/** @var \Light\ObjectService\Resource\ResolvedValue */
	private $sourceResource;
	/** @var string */
	private $path;
	/** @var array */
	private $elements = array();

	/**
	 * Creates a new ResourcePath object from the resource path portion of the URL string.
	 * @param ResolvedValue	$source
	 * @param string|array	$path
	 * @return ResourcePath
	 */
	public static function create(ResolvedValue $source, $path)
	{
		return new self($source, $path);
	}

	/**
	 * Constructs a new ResourcePath object.
	 * @param ResolvedValue $source
	 * @param string|array  $path
	 */
	protected  function __construct(ResolvedValue $source, $path)
	{
		if (is_array($path))
		{
			$this->path = implode("/", $path);
			$pathElements = $path;
		}
		else if ($path[0] == "/")
		{
			throw new InvalidParameterValue('$path', $path, "Resource path cannot start with a '/'");
		}
		else
		{
			// path is a string
			$this->path = $path;
			$pathElements = explode("/", $path);
		}
		$this->sourceResource = $source;
		$this->parse($pathElements);
	}

	/**
	 * Returns a list of parsed path elements.
	 * @return mixed[]
	 */
	public function getElements()
	{
		return $this->elements;
	}

	/**
	 * Returns the original resource path string.
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Returns the resource from which path resolution starts.
	 * @return \Light\ObjectService\Resource\ResolvedValue
	 */
	public function getSourceResource()
	{
		return $this->sourceResource;
	}

	/**
	 * Returns the last element in the parsed path.
	 * @return mixed
	 */
	public function getLastElement()
	{
		return end($this->elements);
	}

	/**
	 * Parse the remainder of the path.
	 * @param array $path
	 */
	protected function parse(array $path)
	{
		$count = count($path);
		foreach($path as $element)
		{
			$count--;

			if (is_numeric($element))
			{
				// a numeric identifier
				$this->elements[] = (integer) $element;
			}
			elseif ($element[0] == "(" && substr($element, -1, 1) == ")")
			{
				// a scope specification
				$this->elements[] = UrlScopeParser::parseIntermediateScope($element);
			}
			else if ($element === "" && $count == 0)
			{
				// an empty scope specification at the end of the path
				$this->elements[] = Scope::createEmptyScope();
			}
			else
			{
				// a property name
				$this->elements[] = (string) $element;
			}
		}
	}
}