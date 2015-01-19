<?php
namespace Light\ObjectService\Resource\Operation;

use Light\ObjectService\Resource\ResolvedValue;
use Light\ObjectService\Resource\ResourceSpecification;
use Light\ObjectService\Type\CollectionTypeInterfaces\Append;

// TODO Implementation has not been finished / tested

class AppendOperation extends Operation
{
	/** @var ResourceSpecification */
	private $resourceSpecification;

	/**
	 * Constructs a new AppendOperation object.
	 * @param ResourceSpecification $subject	Resource to be appended.
	 */
	public function __construct(ResourceSpecification $subject)
	{
		$this->resourceSpecification = $subject;
	}

	/**
	 * Executes the operation.
	 * @param ResolvedValue       $resource
	 * @param ExecutionParameters $params
	 * @return ResolvedValue Result resource
	 */
	public function execute(ResolvedValue $resource, ExecutionParameters $params)
	{
		if (!$resource->isCollection())
		{
			throw new OperationNotAllowed($resource, "Cannot append to a non-collection resource");
		}

		$type = $resource->getType();
		if ($type instanceof Append)
		{
			$subjectResource = $this->resourceSpecification->resolve($params);
			$type->appendValue($resource->getValue(), $subjectResource->getValue());
		}
		else
		{
			throw new OperationNotAllowed($resource, "Collection does not support appending elements");
		}
	}

} 