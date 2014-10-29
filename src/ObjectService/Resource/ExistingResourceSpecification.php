<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Resource\Addressing\ResourceIdentifier;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Type\PathReader;

class ExistingResourceSpecification extends ResourceSpecification
{
	/** @var ResourceIdentifier */
	private $resourceIdentifier;

	public function __construct(ResourceIdentifier $resourceIdentifier)
	{
		$this->resourceIdentifier = $resourceIdentifier;
	}

	/**
	 * @param ExecutionParameters $parameters
	 * @return ResolvedValue
	 */
	public function resolve(ExecutionParameters $parameters)
	{
		$resolved = $this->resourceIdentifier->resolve($parameters->getEndpointRegistry());

		$pathReader = new PathReader($resolved->getResourcePath(), $resolved->getEndpoint()->getObjectRegistry());
		return $pathReader->read();
	}
}