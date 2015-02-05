<?php
namespace Light\ObjectService\Service\Protocol;

use Light\Exception\Exception;
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
		// TODO
	}

	/**
	 * Returns an exception serializer matching the accepted content-type of the client.
	 * @param Request $httpRequest
	 * @return ExceptionSerializer An ExceptionSerializer object, if a matching one is found; otherwise, NULL.
	 */
	public function getExceptionSerializer(Request $httpRequest)
	{
		// TODO
	}

	/**
	 * Returns a resource serializer matching the accepted content-type of the client.
	 * @param Request $httpRequest
	 * @return ResourceSerializer A ResourceSerializer object, if a matching one is found; otherwise, NULL.
	 */
	public function getResourceSerializer(Request $httpRequest)
	{
		// TODO
	}

	/**
	 * Returns a value serializer matching the accepted content-type of the client.
	 * @param Request $httpRequest
	 * @return ValueSerializer A ValueSerializer object, if a matching one is found; otherwise, NULL.
	 */
	public function getValueSerializer(Request $httpRequest)
	{
		// TODO
	}
}