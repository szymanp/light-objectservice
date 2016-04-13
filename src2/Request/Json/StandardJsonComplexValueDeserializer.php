<?php
namespace Szyman\ObjectService\Request\Json;

use Light\ObjectAccess\Type\ComplexTypeHelper;
use Light\ObjectService\Exception\MalformedRequest;
use Szyman\ObjectService\Resource\ExistingResourceReference;
use Szyman\ObjectService\Resource\KeyValueComplexValueRepresentation;
use Szyman\ObjectService\Resource\NewComplexResourceReference;
use Szyman\ObjectService\Service\ComplexValueModification;
use Szyman\ObjectService\Service\ComplexValueModificationDeserializer;
use Szyman\ObjectService\Service\ComplexValueRepresentation;
use Szyman\ObjectService\Service\ComplexValueRepresentationDeserializer;

final class StandardJsonComplexValueDeserializer implements ComplexValueRepresentationDeserializer, ComplexValueModificationDeserializer
{
	const REPLACE = 1;
	const UPDATE = 2;

	/** @var ComplexTypeHelper */
	private $typeHelper;

	private $mode;

	private function __construct(ComplexTypeHelper $typeHelper, $mode)
	{
		$this->typeHelper = $typeHelper;
		$this->mode = $mode;
	}

	/**
	 * Creates a new deserializer for a full object representation.
	 * @param ComplexTypeHelper $typeHelper
	 * @return StandardJsonComplexValueDeserializer
	 */
	public static function newRepresentationDeserializer(ComplexTypeHelper $typeHelper)
	{
		return new self($typeHelper, self::REPLACE);
	}

	/**
	 * Creates a new deserializer for a partial object representation.
	 * @param ComplexTypeHelper $typeHelper
	 * @return StandardJsonComplexValueDeserializer
	 */
	public static function newModificationDeserializer(ComplexTypeHelper $typeHelper)
	{
		return new self($typeHelper, self::UPDATE);
	}

	/**
	 * Deserializes the object.
	 * @param string $content
	 * @return ComplexValueRepresentation|ComplexValueModification
	 * @throws MalformedRequest	Thrown if the content does not match the expected format.
	 */
	public function deserialize($content)
	{
		if (is_resource($content))
		{
			$content = stream_get_contents($content);
			if ($content === false) throw new \RuntimeException("Could not read from stream");
		}
		
		$json = json_decode($content);
		if (is_null($json))
		{
			throw new MalformedRequest("Could not convert request body to JSON");
		}

		$result = $this->readObject($json);
		
		return $result;
	}

	/**
	 * @param \stdClass $json
	 * @return KeyValueComplexValueRepresentation
	 */
	private function readObject(\stdClass $json)
	{
		$result = new KeyValueComplexValueRepresentation();

		foreach($json as $fieldName => $fieldValue)
		{
			if (is_scalar($fieldValue))
			{
				$result->setValue($fieldName, $fieldValue);
			}
			elseif (is_array($fieldValue))
			{
				$result->setArray($fieldName, $this->readList($fieldValue));
			}
			elseif (is_object($fieldValue))
			{
				$result->setResource($fieldName, $this->readReference($fieldValue));
			}
			else
			{
				throw new \LogicException(gettype($fieldValue));
			}
		}

		return $result;
	}

	/**
	 * @param array $list
	 * @return array
	 */
	private function readList(array $list)
	{
		$result = array();

		foreach($list as $element)
		{
			if (is_scalar($element))
			{
				$result[] = $element;
			}
			elseif (is_array($element))
			{
				$result[] = $this->readList($element);
			}
			elseif (is_object($element))
			{
				$result[] = $this->readReference($element);
			}
			else
			{
				throw new \LogicException(gettype($element));
			}
		}

		return $result;
	}

	private function readReference($json)
	{
		if (isset($json->_href))
		{
			// FIXME: We need an address class that accepts a full URL.
	//		return new ExistingResourceReference()
		}
		else
		{
			// FIXME: We don't have a TypeHelper at this point. Change the deserializer interface?
	//		return new NewComplexResourceReference()$this->readObject($json);
		}
	}
}