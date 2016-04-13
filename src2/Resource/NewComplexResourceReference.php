<?php
namespace Szyman\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Type\ComplexTypeHelper;
use Szyman\ObjectService\Service\ExecutionEnvironment;

/**
 * A reference that returns a new complex resource.
 */
final class NewComplexResourceReference extends ResourceReference
{
	/** @var ComplexTypeHelper */
	private $complexTypeHelper;

	/** @var KeyValueComplexValueRepresentation */
	private $representation;

	public function __construct(ComplexTypeHelper $complexTypeHelper, KeyValueComplexValueRepresentation $representation = null)
	{
		$this->complexTypeHelper = $complexTypeHelper;
		$this->representation = $representation;
	}

	/**
	 * Returns the referenced resource.
	 * @param ExecutionEnvironment $environment
	 * @throws ResourceReferenceException
	 * @return ResolvedResource
	 */
	public function resolve(ExecutionEnvironment $environment)
	{
		$resource = $this->complexTypeHelper->createResource($environment->getTransaction());

		if ($this->representation)
		{
			$this->representation->updateObject($resource, $environment);
		}

		return $resource;
	}
}