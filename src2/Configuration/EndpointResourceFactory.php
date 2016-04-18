<?php
namespace Szyman\ObjectService\Configuration;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectAccess\Type\CollectionType;
use Light\ObjectAccess\Type\CollectionTypeHelper;
use Light\ObjectAccess\Type\Type;
use Light\ObjectAccess\Type\TypeHelper;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Szyman\Exception\Exception;
use Szyman\Exception\InvalidArgumentTypeException;

/**
 * A factory that can create resources based on a supplied Endpoint.
 *
 * This class is used as a return value from ObjectProviders, which are endpoint- and type-agnostic.
 */
abstract class EndpointResourceFactory
{
	/** @var string */
	protected $address;
	/** @var Origin */
	protected $origin;

	/**
	 * Creates a factory for values, objects and materialized collections.
	 * @param string $address	An endpoint-relative address.
	 * @param mixed  $value
	 * @param Origin $origin
	 * @return EndpointResourceFactory
	 */
	public static function newValueFactory($address, $value, Origin $origin = null)
	{
		return new EndpointResourceFactory_Value($address, $value, $origin);
	}

	/**
	 * Creates a factory for unmaterialized collections.
	 * @param string $address	An endpoint-relative address.
	 * @param Type|TypeHelper|string  $type		A Type object, a TypeHelper object or a name for a type.
	 * @param Origin 				  $origin
	 * @return EndpointResourceFactory
	 */
	public static function newCollectionFactory($address, $type, Origin $origin = null)
	{
		return new EndpointResourceFactory_Collection($address, $type, $origin);
	}

	protected function __construct($address, Origin $origin = null)
	{
		if (!is_string($address)) throw new InvalidArgumentTypeException('address', $address, 'string');

		$this->address = $address;
		$this->origin = $origin ? $origin : Origin::unavailable();
	}

	/**
	 * Returns a new resource.
	 * @param Endpoint $endpoint
	 * @return ResolvedResource
	 */
	abstract public function createResource(Endpoint $endpoint);
}

/**
 * Creates a new object of a subtype of ResolvedValue.
 */
final class EndpointResourceFactory_Value extends EndpointResourceFactory
{
	/** @var mixed */
	private $value;

	public function __construct($address, $value, Origin $origin = null)
	{
		parent::__construct($address, $origin);
		$this->value = $value;
	}

	public function createResource(Endpoint $endpoint)
	{
		$typeHelper = $endpoint->getTypeRegistry()->getTypeHelperByValue($this->value);
		$address = EndpointRelativeAddress::create($endpoint, $this->address);
		return ResolvedValue::create($typeHelper, $this->value, $address, $this->origin);
	}
}

final class EndpointResourceFactory_Collection extends EndpointResourceFactory
{
	/** @var Type|TypeHelper|string */
	private $type;

	public function __construct($address, $type, Origin $origin = null)
	{
		parent::__construct($address, $origin);
		$this->type = $type;
	}

	public function createResource(Endpoint $endpoint)
	{
		$address = EndpointRelativeAddress::create($endpoint, $this->address);

		if ($this->type instanceof CollectionType)
		{
			$typeHelper = $endpoint->getTypeRegistry()->getTypeHelperByType($this->type);
		}
		elseif ($this->type instanceof CollectionTypeHelper)
		{
			$typeHelper = $this->type;
		}
		elseif (is_string($this->type))
		{
			// Read it by name.
			$typeHelper = $endpoint->getTypeRegistry()->getCollectionTypeHelper($this->type);
		}
		else
		{
			throw new Exception('Factory was created with an invalid $type: %1', $this->type);
		}

		return new ResolvedCollectionResource($typeHelper, $address, $this->origin);
	}
}