<?php
namespace Light\ObjectService\Resource;

use Light\ObjectAccess\Exception\AddressResolutionException;
use Light\ObjectAccess\Resource\Addressing\Address;
use Light\ObjectAccess\Resource\Addressing\RelativeAddress;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Operation\ExecutionParameters;

class ExistingResourceSpecification extends ResourceSpecification
{
	/** @var Address */
	private $address;

	public function __construct(Address $resourceAddress)
	{
		$this->address = $resourceAddress;
	}

	/**
	 * Returns the resource described by this specification.
	 * @param ExecutionParameters $parameters
	 * @return ResolvedResource
	 * @throws AddressResolutionException
	 */
	public function resolve(ExecutionParameters $parameters)
	{
		$address = $this->address;

		if ($address instanceof EndpointRelativeAddress)
		{
			$newAddress = $address->getEndpoint()->findResource($address->getPathElements());

			if (is_null($newAddress))
			{
				throw new AddressResolutionException(
					"Address %1 could not be resolved to a resource",
					$address->hasStringForm() ? '"' . $address->getAsString() . '"' : "(no string form)");
			}

			$address = $newAddress;
		}

		if ($address instanceof RelativeAddress)
		{
			$relativeAddressReader = new RelativeAddressReader($address);
			return $relativeAddressReader->read();
		}
		else
		{
			throw new AddressResolutionException("Not able to resolve an address of type %1", get_class($address));
		}
	}
}