<?php
namespace Light\ObjectService\Service\Protocol;

use Light\Exception\Exception;
use Light\Exception\InvalidParameterType;
use Light\ObjectService\Resource\Projection\DataEntity;
use Symfony\Component\HttpFoundation\Request;

class SerializationHelper
{
	/** @var Deserializer[] */
	private $deserializers = array();
	/** @var ExceptionSerializer[] */
	private $exceptionSerializers = array();
	/** @var ResourceSerializer[] */
	private $resourceSerializers = array();
	/** @var ValueSerializer[] */
	private $valueSerializers = array();

	/**
	 * Adds a deserializer.
	 * @param Deserializer $deserializer
	 * @throws Exception
	 */
	public function addDeserializer(Deserializer $deserializer)
	{
		foreach($deserializer->getContentTypes() as $ct)
		{
			$ct = trim(strtolower($ct));
			if (isset($this->deserializers[$ct]))
			{
				throw new Exception("Another deserializer is already registered for content type '%1'", $ct);
			}
			$this->deserializers[$ct] = $deserializer;
		}
	}

	/**
	 * Adds a serializer.
	 * @param Serializer $serializer
	 * @throws Exception
	 */
	public function addSerializer(Serializer $serializer)
	{
		if ($serializer instanceof ExceptionSerializer)
		{
			$target = &$this->exceptionSerializers;
		}
		elseif ($serializer instanceof ResourceSerializer)
		{
			$target = &$this->resourceSerializers;
		}
		elseif ($serializer instanceof ValueSerializer)
		{
			$target = &$this->valueSerializers;
		}
		else
		{
			throw new \LogicException("Serializers of class " . get_class($serializer) . " are not supported");
		}

		$ct = strtolower(trim($serializer->getContentType()));
		if (isset($target[$ct]))
		{
			throw new Exception("Another serializer is already registered for content type '%1'", $ct);
		}
		$target[$ct] = $serializer;
	}

	/**
	 * Returns a deserializer matching for the content-type of the request entity.
	 * @param Request $httpRequest
	 * @return Deserializer	A Deserializer object, if a matching one is found; otherwise, NULL.
	 */
	public function getDeserializer(Request $httpRequest)
	{
		$contentType = $httpRequest->getContentType();

		if (is_null($contentType))
		{
			// No deserializer can support this.
			return null;
		}

		$contentType = strtolower(trim($contentType));

		if (isset($this->deserializers[$contentType]))
		{
			return $this->deserializers[$contentType];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Returns a serializer matching the given value and the accepted content-type of the client.
	 * @param Request $httpRequest
	 * @param mixed  $value
	 * @return Serializer
	 */
	public function getSerializer(Request $httpRequest, $value)
	{
		if (is_object($value))
		{
			if ($value instanceof \Exception)
			{
				return $this->getExceptionSerializer($httpRequest);
			}
			else if ($value instanceof DataEntity)
			{
				return $this->getResourceSerializer($httpRequest);
			}
		}
		elseif (is_scalar($value))
		{
			return $this->getValueSerializer($httpRequest);
		}

		throw new InvalidParameterType('$value', $value, "Exception|DataEntity|scalar");
	}

	/**
	 * Returns an exception serializer matching the accepted content-type of the client.
	 * @param Request $httpRequest
	 * @return ExceptionSerializer An ExceptionSerializer object, if a matching one is found; otherwise, NULL.
	 */
	public function getExceptionSerializer(Request $httpRequest)
	{
		return $this->pickSerializer($httpRequest, $this->exceptionSerializers);
	}

	/**
	 * Returns a resource serializer matching the accepted content-type of the client.
	 * @param Request $httpRequest
	 * @return ResourceSerializer A ResourceSerializer object, if a matching one is found; otherwise, NULL.
	 */
	public function getResourceSerializer(Request $httpRequest)
	{
		return $this->pickSerializer($httpRequest, $this->resourceSerializers);
	}

	/**
	 * Returns a value serializer matching the accepted content-type of the client.
	 * @param Request $httpRequest
	 * @return ValueSerializer A ValueSerializer object, if a matching one is found; otherwise, NULL.
	 */
	public function getValueSerializer(Request $httpRequest)
	{
		return $this->pickSerializer($httpRequest, $this->valueSerializers);
	}

	/**
	 * Returns a list of content-types that can be deserialized.
	 * @return string[]
	 */
	public function getDeserializableContentTypes()
	{
		return array_keys($this->deserializers);
	}

	/**
	 * Returns a list of content-types that can be serialized.
	 * @return string[]
	 */
	public function getSerializableContentTypes()
	{
		return array_unique(array_merge(array_keys($this->resourceSerializers), array_keys($this->valueSerializers)));
	}

	protected function pickSerializer(Request $httpRequest, array & $pool)
	{
		$contentTypes = $httpRequest->getAcceptableContentTypes();

		if (empty($contentTypes) || empty($pool))
		{
			return null;
		}

		foreach($contentTypes as $contentType)
		{
			$contentType = strtolower(trim($contentType));

			if (isset($pool[$contentType]))
			{
				return $pool[$contentType];
			}
		}

		return null;
	}
}