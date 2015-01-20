<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\Exception\InvalidParameterValue;
use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Resource\Addressing\ResourceAddress;
use Light\ObjectService\Service\Endpoint;
use Light\Util\URL;

/**
 * A resource address that is relative to a service endpoint.
 */
class EndpointRelativeAddress implements ResourceAddress
{
	const SEPARATOR = "/";

	/** @var Endpoint */
	private $endpoint;
	/** @var array */
	private $elements;
	/** @var string */
	private $localAddressString;

	/**
	 * Creates a new address object from an endpoint and a local address string.
	 * @param Endpoint $endpoint
	 * @param string   $localAddress
	 * @return EndpointRelativeAddress
	 */
	public static function create(Endpoint $endpoint, $localAddress = "")
	{
		$address = new self($endpoint);

		// Strip a leading slash
		if ($localAddress[0] == self::SEPARATOR)
		{
			$localAddress = substr($localAddress, 1);
		}

		$address->localAddressString = $localAddress;
		if (empty($localAddress))
		{
			$address->elements = array();
		}
		else
		{
			$address->elements = explode(self::SEPARATOR, $localAddress);
		}
		return $address;
	}

	/**
	 * Constructs a new address.
	 * @param Endpoint $endpoint
	 */
	protected function __construct(Endpoint $endpoint)
	{
		$this->endpoint = $endpoint;
	}

	/**
	 * Returns a new address with the given scope appended at the end.
	 * @param Scope $scope
	 * @return ResourceAddress	A new ResourceAddress object representing the original address
	 *                          with the scope object appended at the end.
	 */
	public function appendScope(Scope $scope)
	{
		$newAddress = $this->getCopy();

		$newAddress->elements[] = $scope;

		if (!is_null($newAddress->localAddressString))
		{
			if ($scope instanceof Scope\KeyScope)
			{
				$newAddress->localAddressString = URL::joinPaths(array($newAddress->localAddressString, (string)$scope->getKey()));
			}
			elseif ($scope instanceof Scope\EmptyScope)
			{
				$newAddress->localAddressString .= self::SEPARATOR;
			}
			else
			{
				$newAddress->localAddressString = null;
			}
		}

		return $newAddress;
	}

	/**
	 * @param string $pathElement
	 * @return ResourceAddress    A new ResourceAddress object representing the original address
	 *                            with the new element appended at the end.
	 */
	public function appendElement($pathElement)
	{
		if (strpos($pathElement, self::SEPARATOR) !== false)
		{
			throw new InvalidParameterValue('$pathElement', $pathElement, "The path element cannot contain the separator character (" . self::SEPARATOR . ")");
		}

		$newAddress = $this->getCopy();
		$newAddress->elements[] = $pathElement;
		if (!is_null($this->localAddressString))
		{
			$newAddress->localAddressString = URL::joinPaths(array($newAddress->localAddressString, $pathElement));
		}
		return $newAddress;
	}

	/**
	 * Returns true if the address has a string representation.
	 * @return boolean
	 */
	public function hasStringForm()
	{
		return !is_null($this->localAddressString);
	}

	/**
	 * Returns the string representation of this address.
	 * @return string	An address string, if it is available; otherwise, NULL.
	 */
	public function getAsString()
	{
		if (is_null($this->localAddressString))
		{
			return null;
		}
		else
		{
			return URL::joinPaths(array($this->endpoint->getUrl(), $this->localAddressString));
		}
	}

	/**
	 * Returns the local part of the address as a string.
	 *
	 * @return string	A string representing the address without the endpoint prefix, if a string form is available;
	 *                	otherwise, NULL.
	 */
	public function getLocalAddressAsString()
	{
		return $this->localAddressString;
	}

	/**
	 * Returns the endpoint that this address is relative to.
	 * @return Endpoint
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	/**
	 * Returns a copy of this address object.
	 * @return EndpointRelativeAddress
	 */
	public function getCopy()
	{
		$address = new self($this->endpoint);
		$address->elements = $this->elements;
		$address->localAddressString = $this->localAddressString;
		return $address;
	}
}