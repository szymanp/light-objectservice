<?php
namespace Szyman\ObjectService\Response;
use Light\ObjectService\Exception\SerializationException;

/**
 * A serializer for converting a primitive value into some standard format such as XML or JSON.
 *
 * The aim of this serializer is to create a representation that closely follows the structure of the input PHP object.
 */
interface DataSerializer
{
	/**
	 * Serializes a primitive value to a byte representation.
	 * @param mixed $data         A value to serialize, which can be a scalar, an array or an object.
	 *                            If the value is an array, then elements of the array must also be values adhering these rules.
	 *                            If the value is an object, then it must be an object of class {@link \stdClass} containing key-value pairs.
	 *                            Each value in the key-value pair must adhere to the rules described here.
	 * @return string|resource
	 * @throws \DomainException			Thrown if the argument does not adhere to the above conditions.
	 * @throws SerializationException	Thrown if there is some other problem serializing the data.
	 */
	public function serializeData($data);
	
	/**
	 * Returns the name of the output data format.
	 * @return string	A name for the format of the output data returned by this serializer;
	 *					it could be a string such as "XML", "JSON", "YAML", etc.
	 */
	public function getFormatName();
}
