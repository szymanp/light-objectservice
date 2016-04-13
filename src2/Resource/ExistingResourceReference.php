<?php
namespace Szyman\ObjectService\Resource;

use Light\ObjectAccess\Resource\Addressing\Address;
use Light\ObjectAccess\Resource\Addressing\RelativeAddress;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Szyman\ObjectService\Service\ExecutionEnvironment;

/**
 * A reference to an existing resource.
 */
final class ExistingResourceReference extends ResourceReference
{
	/** @var Address */
	private $address;

	public function __construct(Address $resourceAddress)
	{
		$this->address = $resourceAddress;
	}

	/**
	 * Returns the referenced resource.
	 * @param ExecutionEnvironment $environment
	 * @throws ResourceReferenceException
	 * @return ResolvedResource
	 */
	public function resolve(ExecutionEnvironment $environment)
	{
		$address = $this->address;

		if ($address instanceof EndpointRelativeAddress)
		{
			$newAddress = $address->getEndpoint()->findResource($address->getPathElements());

			if (is_null($newAddress))
			{
				throw new ResourceReferenceException(
					'Address ' . ($address->hasStringForm() ? '"' . $address->getAsString() . '"' : '(no string form)')
					. ' could not be resolved to a resource');
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
			throw new ResourceReferenceException('Not able to resolve an address of type ' . get_class($address));
		}
	}
}