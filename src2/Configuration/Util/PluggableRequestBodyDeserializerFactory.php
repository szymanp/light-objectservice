<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Type\Type;
use Light\ObjectAccess\Type\SimpleType;
use Light\ObjectAccess\Type\ComplexType;
use Light\ObjectAccess\Type\CollectionType;
use Light\ObjectService\Exception\UnsupportedMediaType;
use Szyman\Exception\InvalidArgumentException;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;

/**
 * A <kbd>RequestBodyDeserializerFactory</kbd> that can route request to other factories based on content-type.
 */
class PluggableRequestBodyDeserializerFactory implements RequestBodyDeserializerFactory
{
	const SIMPLE_VALUE_REPRESENTATION 		= "SimpleValueRepresentation";
	const COMPLEX_VALUE_REPRESENTATION		= "ComplexValueRepresentation";
	const COMPLEX_VALUE_MODIFICATION		= "ComplexValueModification";
	const COLLECTION_VALUE_REPRESENTATION	= "CollectionValueRepresentation";
	
	/** @var PluggableRequestBodyDeserializerFactory_Base[] */
	protected $registrations = array();

	/**
	 * Register a new deserializer factory closure.
	 * @param string	$representation	One of the representation constants defined on this class.
	 * @param string	$contentType	MIME content-type supported by the deserializer.
	 * @param \Closure	$factoryFn		A function taking a Type object and returning an appropriate deserializer instance.
	 * @return $this
	 */
	public function registerDeserializer($representation, $contentType, \Closure $factoryFn)
	{
		$this->registrations[] = new PluggableRequestBodyDeserializerFactory_Deserializer($representation, $contentType, $factoryFn);
		return $this;
	}
	
	/**
	 * Register a new deserializer factory object.
	 * @param string							$contentType	MIME content-type supported by the deserializer.
	 * @param RequestBodyDeserializerFactory	$factory		A factory which will be used for creating deserializers
	 *															for this content-type.
	 * @return $this
	 */
	public function registerFactory($contentType, RequestBodyDeserializerFactory $factory)
	{
		$this->registrations[] = new PluggableRequestBodyDeserializerFactory_Factory($contentType, $factory);
		return $this;
	}
	
	/**
	 * Creates a new deserializer for a full representation of a simple value.
	 * @param string     $contentType
	 * @param SimpleType $simpleType
	 * @throws UnsupportedMediaType	If the factory cannot handle the specified content-type.
	 * @return SimpleValueRepresentationDeserializer
	 */
	public function newSimpleValueRepresentationDeserializer($contentType, SimpleType $simpleType)
	{
		return $this->findOrThrow(self::SIMPLE_VALUE_REPRESENTATION, $simpleType);
	}

	/**
	 * Creates a new deserializer for a full representation of an object.
	 * @param string      $contentType
	 * @param ComplexType $complexType
	 * @throws UnsupportedMediaType	If the factory cannot handle the specified content-type.
	 * @return ComplexValueRepresentationDeserializer
	 */
	public function newComplexValueRepresentationDeserializer($contentType, ComplexType $complexType)
	{
		return $this->findOrThrow(self::COMPLEX_VALUE_REPRESENTATION, $complexType);
	}

	/**
	 * Creates a new deserializer for a partial representation of an object.
	 * @param string      $contentType
	 * @param ComplexType $complexType
	 * @throws UnsupportedMediaType	If the factory cannot handle the specified content-type.
	 * @return ComplexValueModificationDeserializer
	 */
	public function newComplexValueModificationDeserializer($contentType, ComplexType $complexType)
	{
		return $this->findOrThrow(self::COMPLEX_VALUE_MODIFICATION, $complexType);
	}

	/* TODO
	public function newCollectionValueRepresentationDeserializer($contentType, CollectionType $type);

	public function newCollectionValueModificationDeserializer($contentType, CollectionType $type);

	public function newCollectionElementSelectionDeserializer($contentType, CollectionType $type);

	public function newComplexValueActionDeserializer($contentType, ComplexType $type);

	public function newCollectionxValueActionDeserializer($contentType, CollectionType $type);
	*/

	protected function findOrThrow($representation, $contentType, Type $type)
	{
		foreach($this->registrations as $reg)
		{
			if (!is_null($dsz = $reg->getDeserializerIfMatches($representation, $contentType, $type)))
			{
				return $dsz;
			}
		}
		throw new UnsupportedMediaType($contentType);
	}
}

abstract class PluggableRequestBodyDeserializerFactory_Base
{
	protected $contentType;
	
	public function __construct($contentType)
	{
		if (!is_string($contentType))
		{
			throw InvalidArgumentException::newInvalidType('$contentType', $contentType, "string");
		}
		if (empty($contentType))
		{
			throw InvalidArgumentException::newInvalidValue('$contentType', $contentType, "Content-type cannot be empty");
		}
		$this->contentType = $contentType;
	}
	
	/**
	 * @return bool	True, if the content type matches; otherwise, false.
	 */
	public function contentTypeMatches($contentType)
	{
		return strtolower($this->contentType) === strtolower($contentType);
	}

	abstract public function getDeserializerIfMatches($representation, $contentType, Type $type);
}

final class PluggableRequestBodyDeserializerFactory_Deserializer extends PluggableRequestBodyDeserializerFactory_Base
{
	private $representation;
	private $factoryFn;
	
	private static $representations = array(
		PluggableRequestBodyDeserializerFactory::SIMPLE_VALUE_REPRESENTATION,
		PluggableRequestBodyDeserializerFactory::COMPLEX_VALUE_REPRESENTATION,
		PluggableRequestBodyDeserializerFactory::COMPLEX_VALUE_MODIFICATION,
		PluggableRequestBodyDeserializerFactory::COLLECTION_VALUE_REPRESENTATION);

	public function __construct($contentType, $representation, \Closure $factoryFn)
	{
		parent::__construct($contentType);
		if (!in_array($representation, self::$representations))
		{
			throw InvalidArgumentException::newInvalidValue('$representation', $representation);
		}
		$this->representation = $representation;
		$this->factoryFn = $factoryFn;
	}
	
	public function getDeserializerIfMatches($representation, $contentType, Type $type)
	{
		if ($representation === $this->representation
			&& $this->contentTypeMatches($contentType))
		{
			$fn = $this->factoryFn;
			return $fn($type);
		}
		return NULL;
	}
}

final class PluggableRequestBodyDeserializerFactory_Factory extends PluggableRequestBodyDeserializerFactory_Base
{
	private $factory;

	public function __construct($contentType, $factory)
	{
		parent::__construct($contentType);
		$this->factory = $factory;
	}

	public function getDeserializerIfMatches($representation, $contentType, Type $type)
	{
		if ($this->contentTypeMatches($contentType))
		{
			switch ($representation)
			{
				case PluggableRequestBodyDeserializerFactory::SIMPLE_VALUE_REPRESENTATION:
					return $this->factory->newSimpleValueRepresentation($contentType, $type);
				case PluggableRequestBodyDeserializerFactory::COMPLEX_VALUE_REPRESENTATION:
					return $this->factory->newComplexValueRepresentation($contentType, $type);
				case PluggableRequestBodyDeserializerFactory::COMPLEX_VALUE_MODIFICATION:
					return $this->factory->newComplexValueModification($contentType, $type);
				case PluggableRequestBodyDeserializerFactory::COLLECTION_VALUE_REPRESENTATION:
					return $this->factory->newCollectionValueRepresentation($contentType, $type);
			}
		}
		return NULL;
	}
}
