<?php
namespace Szyman\ObjectService\Response;

/**
 * A serializer for converting a primitive PHP object into some standard format such as XML or JSON.
 *
 * The aim of this serializer is to create a representation that closely follows the structure of the input PHP object.
 */
interface DataSerializer
{
	/**
	 * Serializes a primitive PHP object to a byte representation.
	 * @param \stdClass $data	A primitive PHP object containing key-value pairs.
	 *                        	Each value can be a PHP scalar, array or another primitive PHP object.
	 *                        	In case of arrays, the values must also obey the above conditions.
	 * TODO: Is it correct that this method accepts a \stdClass object?
	 *       A primitive value could be serialized to, say, an int. We would still want the data serializer to process it.
	 * @return string|resource
	 * TODO: What else does it throw?
	 * @throws \DomainException	Thrown if the object does not adhere to the above conditions.
	 */
	public function serializeData(\stdClass $data);
	
	/**
	 * Returns the name of the output data format.
	 * @return string	A name for the format of the output data returned by this serializer;
	 *					it could be a string such as "XML", "JSON", "YAML", etc.
	 */
	public function getFormatName();
}
