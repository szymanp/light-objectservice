<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Exceptions\ResolutionException;
use Light\ObjectService\Expression\ParsedRootPathExpression;
use Light\ObjectService\Expression\PathExpression;
use Light\ObjectService\Expression\WhereExpressionSource;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Type\PathReader;

/**
 * A specification identifying the resource by an URL.
 */
class UrlResourceSpecification extends ResourceSpecification
{
	/** @var string */
	private $url;
	/** @var array<string, \Light\ObjectService\Expression\WhereExpressionSource> */
	private $queries = array();
	
	/**
	 * Creates a new UrlResourceSpecification. 
	 * @param string	$url
	 * @param array 	$queries
	 * @return \Light\ObjectService\Resource\UrlResourceSpecification
	 */
	public static function create($url, array $queries = array())
	{
		$spec = new self();
		$spec->url 		= $url;
		$spec->queries 	= $queries;
		return $spec; 
	}
	
	/**
	 * Returns the URL for the resource.
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @param string                $name
	 * @param WhereExpressionSource $whereExpressionSource
	 */
	public function addQuery($name, WhereExpressionSource $whereExpressionSource)
	{
		$this->queries[$name] = $whereExpressionSource;
	}

	/**
	 * @return array
	 */
	public function getQueries()
	{
		return $this->queries;
	}

	protected function readBaseResource(ExecutionParameters $parameters)
	{
		$objectRegistry = $parameters->getObjectRegistry();
		$resourceBaseUri = $objectRegistry->getNameRegistry()->getResourceBaseUri();

		if (substr($this->url, 0, strlen($resourceBaseUri)) != $resourceBaseUri)
		{
			throw new ResolutionException("URI <%1> cannot be resolved to a resource", $this->url);
		}

		$relativeUri = substr($this->url, strlen($resourceBaseUri));

		$pathExpression = new PathExpression();
		$pathExpression->setPath($relativeUri);
		foreach($this->queries as $key => $query)
		{
			$pathExpression->setWhereReference($key, $query);
		}

		$parsedPathExpression = new ParsedRootPathExpression($pathExpression, $objectRegistry);
		$pathReader = new PathReader($parsedPathExpression, $objectRegistry);

		return $pathReader->read();
	}
}