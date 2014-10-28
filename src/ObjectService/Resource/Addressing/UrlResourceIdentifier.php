<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\EndpointRegistry;
use Light\ObjectService\Exceptions\ResolutionException;
use Light\ObjectService\Resource\Query\Scope;

final class UrlResourceIdentifier extends ResourceIdentifier
{
	/** @var string */
	private $url;

	public function __construct($url, Scope $scope = null)
	{
		$this->url = $url;
		// TODO external scope handling
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function resolve(EndpointRegistry $registry)
	{
		$endpoint = $registry->findEndpoint($this->url);
		if (is_null($endpoint))
		{
			throw new ResolutionException("URL \"%1\" does not correspond to any known service endpoint", $this->url);
		}

		$url = substr($this->url, strlen($endpoint->getUrl()));

		$urlParts = $this->getUrlParts($url);
		$resourcePathString = $urlParts[0];

		// Find the resource matching the beginning of the path
		$result = $endpoint->getObjectRegistry()->findResource(explode("/", $resourcePathString));
		if (is_null($result))
		{
			throw new ResolutionException("Could not resolve root resource for path \"%1\" of URL \"%2\"", $resourcePathString, $this->url);
		}

		$resourcePath = ResourcePath::create($result->resource, $result->remainder);

		// TODO Parse query part of URL

		return $resourcePath;
	}

	/**
	 * @return array	A three element array containing respectively:
	 *					the address, the query, the hash part.
	 */
	private function getUrlParts($url)
	{
		$tok1 = strtok($url, "?#");
		$sep1 = substr($url, strlen($tok1), 1);
		$tok2 = strtok("#");
		$tok3 = strtok("");

		if ($tok2 === false)
		{
			return array($tok1, false, false);
		}
		else if ($tok3 !== false)
		{
			if ($sep1 !== "?")
			{
				throw new Exception("Query part must come before hash in URL");
			}
			return array($tok1, $tok2, $tok3);
		}
		else if ($sep1 == "?")
		{
			return array($tok1, $tok2, false);
		}
		else
		{
			return array($tok1, false, $tok2);
		}

		return array($tok1, $sep1, $tok2, $tok3);
	}
}
