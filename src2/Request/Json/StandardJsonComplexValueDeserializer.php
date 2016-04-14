<?php
namespace Szyman\ObjectService\Request\Json;

use Light\ObjectAccess\Exception\TypeException;
use Light\ObjectAccess\Type\CollectionTypeHelper;
use Light\ObjectAccess\Type\ComplexTypeHelper;
use Light\ObjectAccess\Type\TypeHelper;
use Light\ObjectService\Exception\MalformedRequest;
use Light\ObjectService\Resource\Addressing\UrlUnresolvedAddress;
use Szyman\ObjectService\Resource\ExistingResourceReference;
use Szyman\ObjectService\Resource\KeyValueComplexValueRepresentation;
use Szyman\ObjectService\Resource\NewComplexResourceReference;
use Szyman\ObjectService\Resource\ResourceReference;
use Szyman\ObjectService\Service\ComplexValueModification;
use Szyman\ObjectService\Service\ComplexValueModificationDeserializer;
use Szyman\ObjectService\Service\ComplexValueRepresentation;
use Szyman\ObjectService\Service\ComplexValueRepresentationDeserializer;

final class StandardJsonComplexValueDeserializer implements ComplexValueRepresentationDeserializer, ComplexValueModificationDeserializer
{
	/** @var ComplexTypeHelper */
	private $typeHelper;

	/**
	 * Creates a new deserializer.
	 * @param ComplexTypeHelper $typeHelper
	 */
	public function __construct(ComplexTypeHelper $typeHelper)
	{
		$this->typeHelper = $typeHelper;
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
			throw new MalformedRequest('Could not convert request body to JSON: ' . json_last_error_msg());
		}

		try
		{
			$result = $this->readObject($json, $this->typeHelper);
		}
		catch (TypeException $e)
		{
			throw new MalformedRequest($e);
		}

		return $result;
	}

	/**
	 * @param \stdClass $json
	 * @return KeyValueComplexValueRepresentation
	 * @throws TypeException
	 */
	private function readObject(\stdClass $json, ComplexTypeHelper $typeHelper)
	{
		$result = new KeyValueComplexValueRepresentation();

		foreach($json as $fieldName => $fieldValue)
		{
			$fieldTypeHelper = $typeHelper->getPropertyTypeHelper($fieldName);

			if (is_scalar($fieldValue))
			{
				$result->setValue($fieldName, $fieldValue);
			}
			elseif (is_array($fieldValue))
			{
				if ($fieldTypeHelper instanceof CollectionTypeHelper)
				{
					$result->setArray($fieldName, $this->readList($fieldValue, $fieldTypeHelper));
				}
				else
				{
					throw new TypeException('Property %1::%2 is not an collection', $typeHelper->getName(), $fieldName);
				}
			}
			elseif (is_object($fieldValue))
			{
				$result->setResource($fieldName, $this->readReference($fieldValue, $fieldTypeHelper));
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
	 * @throws TypeException
	 */
	private function readList(array $list, CollectionTypeHelper $typeHelper)
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
				$baseTypeHelper = $typeHelper->getBaseTypeHelper();
				if ($baseTypeHelper instanceof CollectionTypeHelper)
				{
					$result[] = $this->readList($element, $baseTypeHelper);
				}
				else
				{
					throw new TypeException('List element is not of a collection type');
				}
			}
			elseif (is_object($element))
			{
				$result[] = $this->readReference($element, $typeHelper->getBaseTypeHelper());
			}
			else
			{
				throw new \LogicException(gettype($element));
			}
		}

		return $result;
	}

	/**
	 * @param \stdClass  $json
	 * @param TypeHelper $typeHelper
	 * @return ResourceReference
	 * @throws TypeException
	 * @throws MalformedRequest
	 */
	private function readReference(\stdClass $json, TypeHelper $typeHelper)
	{
		if (isset($json->_href))
		{
			try
			{
				return new ExistingResourceReference(new UrlUnresolvedAddress($json->_href));
			}
			catch (\InvalidArgumentException $e)
			{
				throw new MalformedRequest($e);
			}
		}
		elseif ($typeHelper instanceof ComplexTypeHelper)
		{
			return new NewComplexResourceReference($typeHelper, $this->readObject($json, $typeHelper));
		}
		else
		{
			throw new TypeException('Only references to objects are supported');
		}
	}
}