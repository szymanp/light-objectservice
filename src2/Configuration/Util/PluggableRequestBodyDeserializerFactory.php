<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Type\TypeHelper;
use Szyman\Exception\InvalidArgumentException;
use Szyman\Exception\UnexpectedValueException;
use Szyman\ObjectService\Service\RequestBodyDeserializer;
use Szyman\ObjectService\Service\RequestBodyDeserializerFactory;
use Szyman\ObjectService\Service\RequestBodyDeserializerType;

/**
 * A <kbd>RequestBodyDeserializerFactory</kbd> that can route request to other factories based on content-type.
 */
class PluggableRequestBodyDeserializerFactory implements RequestBodyDeserializerFactory
{
	/** @var PluggableRequestBodyDeserializerFactory_Base[] */
	protected $registrations = array();

	/**
	 * Register a new deserializer factory closure.
	 * @param RequestBodyDeserializerType $deserializerType
	 * @param string					  $contentType		MIME content-type supported by the deserializer.
	 * @param \Closure					  $factoryFn		A function taking a TypeHelper object and returning an appropriate deserializer instance.
	 * @return $this
	 */
	public function registerDeserializer(RequestBodyDeserializerType $deserializerType, $contentType, \Closure $factoryFn)
	{
		$this->registrations[] = new PluggableRequestBodyDeserializerFactory_Deserializer($contentType, $deserializerType, $factoryFn);
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
	 * Creates a new deserializer for the specified body type and resource type.
	 * @param RequestBodyDeserializerType $deserializerType
	 * @param string         			  $contentType
	 * @param TypeHelper      			  $typeHelper
	 * @return RequestBodyDeserializer    A deserializer, if this factory supports creating deserializers matching
	 *                                    the specified parameters; otherwise, NULL.
	 */
	public function newRequestBodyDeserializer(RequestBodyDeserializerType $deserializerType, $contentType, TypeHelper $typeHelper)
	{
		foreach($this->registrations as $reg)
		{
			if (!is_null($dsz = $reg->getDeserializerIfMatches($deserializerType, $contentType, $typeHelper)))
			{
				if ($dsz instanceof RequestBodyDeserializer)
				{
					return $dsz;
				}
				else
				{
					throw UnexpectedValueException::newInvalidReturnValue($reg, 'getDeserializerIfMatches', $dsz, "Expecting " . RequestBodyDeserializer::class);
				}

			}
		}
		return null;
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

	abstract public function getDeserializerIfMatches(RequestBodyDeserializerType $deserializerType, $contentType, TypeHelper $typeHelper);
}

final class PluggableRequestBodyDeserializerFactory_Deserializer extends PluggableRequestBodyDeserializerFactory_Base
{
	private $deserializerType;
	private $factoryFn;
	
	public function __construct($contentType, RequestBodyDeserializerType $deserializerType, \Closure $factoryFn)
	{
		parent::__construct($contentType);
		$this->deserializerType = $deserializerType;
		$this->factoryFn = $factoryFn;
	}
	
	public function getDeserializerIfMatches(RequestBodyDeserializerType $deserializerType, $contentType, TypeHelper $typeHelper)
	{
		if ($deserializerType === $this->deserializerType
			&& $this->contentTypeMatches($contentType))
		{
			$fn = $this->factoryFn;
			return $fn($typeHelper);
		}
		return NULL;
	}
}

final class PluggableRequestBodyDeserializerFactory_Factory extends PluggableRequestBodyDeserializerFactory_Base
{
	/** @var RequestBodyDeserializerFactory */
	private $factory;

	public function __construct($contentType, RequestBodyDeserializerFactory $factory)
	{
		parent::__construct($contentType);
		$this->factory = $factory;
	}

	public function getDeserializerIfMatches(RequestBodyDeserializerType $deserializerType, $contentType, TypeHelper $typeHelper)
	{
		if ($this->contentTypeMatches($contentType))
		{
			return $this->factory->newRequestBodyDeserializer($deserializerType, $contentType, $typeHelper);
		}
		return NULL;
	}
}
