<?php
namespace Light\ObjectService\Resource;

use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Type\ComplexTypeHelper;
use Szyman\ObjectService\Service\ExecutionParameters;
use Light\ObjectService\Resource\Operation\UpdateOperation;

class NewResourceSpecification extends ResourceSpecification
{
	/** @var ComplexTypeHelper */
	private $complexTypeHelper;

	/** @var UpdateOperation */
	private $updateOperation;

	public function __construct(ComplexTypeHelper $complexTypeHelper, UpdateOperation $updateOperation = null)
	{
		$this->complexTypeHelper = $complexTypeHelper;
		$this->updateOperation = $updateOperation;
	}

	/**
	 * Returns the new resource.
	 * @param \Szyman\ObjectService\Service\ExecutionParameters $parameters
	 * @return ResolvedResource	A new resource, which does not have any address nor origin associated with it.
	 */
	public function resolve(ExecutionParameters $parameters)
	{
		$resource = $this->complexTypeHelper->createResource($parameters->getTransaction());

		if ($this->updateOperation)
		{
			$this->updateOperation->execute($resource, $parameters);
		}

		return $resource;
	}
}