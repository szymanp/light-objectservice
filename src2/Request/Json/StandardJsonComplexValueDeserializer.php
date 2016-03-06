<?php
namespace Szyman\ObjectService\Request\Json;

use Light\ObjectAccess\Type\ComplexType;
use Szyman\ObjectService\Service\ComplexValueModification;
use Szyman\ObjectService\Service\ComplexValueModificationDeserializer;
use Szyman\ObjectService\Service\ComplexValueRepresentation;
use Szyman\ObjectService\Service\ComplexValueRepresentationDeserializer;

class StandardJsonComplexValueDeserializer implements ComplexValueRepresentationDeserializer, ComplexValueModificationDeserializer
{
	// TODO: Maybe it would be better to implement this class as an abstract class with two concrete ones.

	/** @var ComplexType */
	private $type;

	private $replace;

	protected function __construct(ComplexType $type, $replace = false)
	{
		// TODO
	}

	/**
	 * Creates a new deserializer for a full object representation.
	 * @param ComplexType $type
	 * @return StandardJsonComplexValueDeserializer
	 */
	public static function newRepresentationDeserializer(ComplexType $type)
	{
		return new self($type, true);
	}

	/**
	 * Creates a new deserializer for a partial object representation.
	 * @param ComplexType $type
	 * @return StandardJsonComplexValueDeserializer
	 */
	public static function newModificationDeserializer(ComplexType $type)
	{
		return new self($type, false);
	}

	/**
	 * Deserializes the object.
	 * @param string $content
	 * @return ComplexValueRepresentation|ComplexValueModification
	 */
	public function deserialize($content)
	{
		// TODO
	}
}