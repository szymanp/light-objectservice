<?php
namespace Szyman\ObjectService\Request\Json;

use Light\ObjectAccess\Type\ComplexTypeHelper;
use Light\ObjectService\Exception\MalformedRequest;
use Szyman\ObjectService\Resource\KeyValueComplexValueRepresentation;
use Szyman\ObjectService\Service\ComplexValueModification;
use Szyman\ObjectService\Service\ComplexValueModificationDeserializer;
use Szyman\ObjectService\Service\ComplexValueRepresentation;
use Szyman\ObjectService\Service\ComplexValueRepresentationDeserializer;

class StandardJsonComplexValueDeserializer implements ComplexValueRepresentationDeserializer, ComplexValueModificationDeserializer
{
	const REPLACE = 1;
	const UPDATE = 2;

	// TODO: Maybe it would be better to implement this class as an abstract class with two concrete ones.

	/** @var ComplexTypeHelper */
	private $typeHelper;

	private $mode;

	protected function __construct(ComplexTypeHelper $typeHelper, $mode)
	{
		// TODO
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
		
		$json = json_decode($content)
		if (is_null($json))
		{
			throw new MalformedRequest("Could not convert request body to JSON");
		}
		
		
		$result = $this->readJson($json);
		
		return $result;
	}
	
	private function readJson($json)
	{
		$result = new KeyValueComplexValueRepresentation();

		foreach($json as $fieldName => $fieldValue)
		{
			if (is_scalar($fieldValue))
			{
				$result->setValue($fieldName, $fieldValue);
			}
			// TODO
		}
	}
}