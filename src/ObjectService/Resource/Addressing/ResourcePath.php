<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\Exception\InvalidParameterValue;
use Light\ObjectService\Resource\Query\UrlScopeParser;

/**
 * The part of the URL representing the local path to a given resource.
 */
final class ResourcePath
{
	private $pathString;
	private $elements;

	// TODO Make a RelativeResourcePath subclass.
	//		Change create($path) -> create($path, ResolvedValue $relativeTo = null)
	//		- if 2nd arg is not null, then it would return a RelativeResourcePath
	//		Then PathReader will now where to start.
	//
	//	 	Will ResourcePath be used as input to PathReader?
	//		I guess to obtain the base object - yes.
	//		But ultimately, whoever is reading the resource, they need to interact with ResourceIdentifier.

	/**
	 * Creates a new ResourcePath object from the resource path portion of the URL string.
	 * @param $path
	 * @return ResourcePath
	 */
	public static function create($path)
	{
		if ($path[0] == "/")
		{
			throw new InvalidParameterValue('$path', $path, "Resource path cannot start with a '/'");
		}

		return new self($path);
	}

	protected  function __construct($path)
	{
		$this->pathString = $path;

		$pathElements = explode("/", $path);
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
		return $this->pathString;
	}

	/**
	 * Parse the remainder of the path.
	 * @param array $path
	 */
	protected function parse(array $path)
	{
		foreach($path as $element)
		{
			if (is_numeric($element))
			{
				// a numeric identifier
				$this->elements[] = (integer) $element;
			}
			elseif ($element[0] == "(" && substr($element, -1, 1) == ")")
			{
				// a scope specification
				$this->elements = UrlScopeParser::parseIntermediateScope($element);
			}
			else
			{
				// a property name
				$this->elements[] = (string) $element;
			}
		}
	}
}