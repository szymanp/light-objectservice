<?php
namespace Light\ObjectService\Resource\Operation;

use Light\Exception\InvalidParameterValue;
use Light\ObjectAccess\Exception\AddressResolutionException;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectService\Resource\Addressing\UrlRelativeAddress;

class TraverseOperation extends Operation
{
	/** @var array<string, Operation[]> */
	private $traversals = array();

	/**
	 * Adds a new traversal operation.
	 * @param string	$addressFragment
	 * @param Operation $operation
	 */
	public function addOperation($addressFragment, Operation $operation)
	{
		if (empty($addressFragment))
		{
			throw new InvalidParameterValue('$addressFragment', $addressFragment, "Address fragment cannot be empty");
		}
		$this->traversals[$addressFragment][] = $operation;
	}

	/**
	 * Executes the operation.
	 * @param ResolvedResource    $resource
	 * @param ExecutionParameters $parameters
	 */
	public function execute(ResolvedResource $resource, ExecutionParameters $parameters)
	{
		foreach($this->traversals as $addressFragment => $operations)
		{
			$address = new UrlRelativeAddress($resource);
			$address->appendFragment($addressFragment);

			$reader = new RelativeAddressReader($address);
			$targetResource = $reader->read();

			if (is_null($targetResource))
			{
				throw new AddressResolutionException(
					"No resource found matching relative address \"%1\" to %2",
					$addressFragment,
					$resource->getAddress()->getAsString());
			}

			foreach($operations as $operation)
			{
				// Execute the operation but discard the resulting resource.
				$operation->execute($targetResource, $parameters);
			}
		}
	}
}