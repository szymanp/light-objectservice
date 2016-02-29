<?php
namespace Szyman\ObjectService\Configuration;

use Light\ObjectAccess\Resource\Addressing\RelativeAddress;
use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectAccess\Resource\Util\DefaultRelativeAddress;
use Light\ObjectAccess\Type\SimpleTypeHelper;
use Light\ObjectAccess\Type\TypeProvider;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;

/**
 * Provides information about a service endpoint.
 */
final class Endpoint
{
	/**
	 * The primary URL of this endpoint.
	 * @var string
	 */
	private $url;

	/**
	 * The alternative URLs (aliases) of this endpoint.
	 * @var string[]
	 */
	private $alternativeUrls = array();

	/** @var ObjectProvider */
	private $objectProvider;

	/** @var TypeRegistry */
	private $typeRegistry;

	/**
	 * Creates a new Endpoint with the given URL.
	 * @param string			$url				The primary URL of this endpoint.
	 * @param ObjectProvider	$objectProvider
	 * @param TypeProvider		$typeProvider
	 * @return Endpoint
	 */
	public static function create($url, ObjectProvider $objectProvider, TypeProvider $typeProvider)
	{
		return new self($url, $objectProvider, $typeProvider);
	}

	/**
	 * Constructs a new Endpoint.
	 * @param string			$url
	 * @param ObjectProvider	$objectProvider
	 * @param TypeProvider		$typeProvider
	 */
	private function __construct($url, ObjectProvider $objectProvider, TypeProvider $typeProvider)
	{
		if (substr($url, -1, 1) != "/")
		{
			$url .= "/";
		}

		$this->url = $url;
		$this->objectProvider = $objectProvider;
		$this->typeRegistry   = new TypeRegistry($typeProvider);
	}

	/**
	 * Adds an alternative URL (alias) for this endpoint.
	 * @param string	$url
	 * @return $this
	 */
	public function addAlternativeUrl($url)
	{
		if ($url !== $this->url && !in_array($url, $this->alternativeUrls, true))
		{
			$this->alternativeUrls[] = $url;
		}
		return $this;
	}

	/**
	 * Returns the URL of this endpoint.
	 * @return string
	 */
	public function getPrimaryUrl()
	{
		return $this->url;
	}

	/**
	 * Returns the alternative URLs (aliases) of this endpoint.
	 * @return string[]
	 */
	public function getAlternativeUrls()
	{
		return $this->alternativeUrls;
	}

	/**
	 * Returns a list of all URLs used by this endpoint.
	 * @return string[]
	 */
	public function getUrls()
	{
		return array_merge([$this->url], $this->alternativeUrls);
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
				$resolvedAddress .= '/';
			}
			$resolvedAddress .= $elem;

			$resource = $this->getResolvedResource($resolvedAddress);

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

	/**
	 * Returns a resolved resource from the ObjectProvider corresponding to the given address.
	 * @param string $address
	 * @return ResolvedResource
	 * @throws \Light\ObjectAccess\Exception\TypeException
	 */
	private function getResolvedResource($address)
	{
		$factory = $this->objectProvider->getResourceFactory($address);
		if (!is_null($factory))
		{
			return $factory->createResource($this);
		}

		return null;
	}
} 