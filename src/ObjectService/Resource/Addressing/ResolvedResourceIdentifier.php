<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\Service\Endpoint;
use Light\ObjectService\Exception\ResolutionException;

class ResolvedResourceIdentifier extends ResourceIdentifier
{
	/**
	 * The service endpoint that this resource belongs to.
	 * @var Endpoint
	 */
	private $endpoint;

	/** @var ResourcePath */
	private $resourcePath;

	/** @var boolean */
	private $openCollection;

	protected function __construct(EndpointRegistry $registry, ResourceIdentifier $resourceIdentifier)
	{
		parent::__construct($resourceIdentifier->url);

		$this->endpoint = $registry->findEndpoint($resourceIdentifier->url);
		if (is_null($this->endpoint))
		{
			throw new ResolutionException("URL \"%1\" does not correspond to any known service endpoint", $resourceIdentifier->url);
		}

		$url = substr($this->getUrl(), strlen($resolved->endpoint->getUrl()));
		$parts = $this->getUrlParts($url);
		
		$resourcePath = ResourcePath::create($parts[0]);
		
		// Find the resource matching the beginning of the path
		$result = $endpoint->getObjectRegistry()->findResource(explode("/", $resourcePath));
		if (is_null($result))
		{
			throw new ResolutionException("Could not resolve root model for path \"%1\" of URL \"%2\"", $resourcePath, $url);
		}
		

	}

	public function getBaseResource()
	{
		// TODO
	}

	public function getResourcesInScope()
	{
		// TODO
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