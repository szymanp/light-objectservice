<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Resource\Addressing\RelativeAddress;
use Light\ObjectAccess\Resource\Util\DefaultRelativeAddress;
use Light\ObjectAccess\Type\TypeRegistry;

/**
 * Provides information about a service endpoint.
 */
final class Endpoint
{
	/**
	 * The URL of this endpoint.
	 * @var string
	 */
	private $url;

	/** @var ObjectProvider */
	private $objectProvider;

	/** @var TypeRegistry */
	private $typeRegistry;

	/**
	 * Creates a new Endpoint with the given URL.
	 * @param string	$url
	 * @return Endpoint
	 */
	public static function create($url, TypeRegistry $typeRegistry, ObjectProvider $objectProvider)
	{
		return new self($url, $typeRegistry, $objectProvider);
	}

	/**
	 * Constructs a new Endpoint.
	 * @param string	$url
	 */
	private function __construct($url, TypeRegistry $typeRegistry, ObjectProvider $objectProvider)
	{
		if (substr($url, -1, 1) != "/")
		{
			$url .= "/";
		}

		$this->url = $url;
		$this->typeRegistry = $typeRegistry;
		$this->objectProvider = $objectProvider;
		$this->objectProvider->setEndpoint($this);
	}

	/**
	 * Returns the URL of this endpoint.
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Returns the TypeRegistry for this service endpoint.
	 * @return TypeRegistry
	 */
	public function getTypeRegistry()
	{
		return $this->typeRegistry;
	}

	/**
	 * Returns a resource matching the beginning of the path.
	 *
	 * For example, if a resource 'models/post' is published, then it would be found
	 * if a path like ["models", "post", "12", "title"] is specified.
	 *
	 * @param array $path
	 * @return RelativeAddress	A RelativeAddress object, if the path prefix resolved to a published resource;
	 *                         	otherwise, NULL.
	 */
	public function findResource(array $path)
	{
		$remainder = $path;
		$resolvedAddress = "";
		foreach($path as $elem)
		{
			array_shift($remainder);

			if (!empty($resolvedAddress))
			{
				$resolvedAddress .= $this->objectProvider->getAddressElementSeparator();
			}
			$resolvedAddress .= $elem;

			$resource = $this->objectProvider->getResource($resolvedAddress);

			if (!is_null($resource))
			{
				$relativeAddress = new DefaultRelativeAddress($resource);

				foreach($remainder as $r)
				{
					$relativeAddress->appendElement($r);
				}

				return $relativeAddress;
			}
		}

		return null;
	}
} 