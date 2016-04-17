<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectAccess\Type\SimpleTypeHelper;
use Light\ObjectService\Exception\SerializationException;
use Light\ObjectService\Resource\Projection\DataCollection;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Resource\Projection\DataObject;
use Szyman\Exception\InvalidArgumentException;

/**
 * Hypertext Application Language serializer.
 *
 * This class serializes objects using the HAL format, as described in
 * https://tools.ietf.org/html/draft-kelly-json-hal-06
 *
 */
final class HalSerializer implements StructureSerializer
{
	private static $reservedPropertyNames = ['_links', '_embedded'];

	/** @inheritdoc */
	public function serializeStructure(DataEntity $dataEntity)
	{
		return $this->serializeDocument($dataEntity);
	}

	/**
	 * Serializes a DataEntity to a separate HAL document.
	 * @param DataEntity $dataEntity
	 * @return \stdClass
	 */
	private function serializeDocument(DataEntity $dataEntity)
	{
		$document = new \stdClass;

		// self link
		if ($dataEntity->getResourceAddress()->hasStringForm())
		{
			$this->addLink($document, 'self', $dataEntity->getResourceAddress()->getAsString());
		}

		// type link
		if ($dataEntity instanceof DataCollection)
		{
			$this->addLink($document, 'type', $dataEntity->getTypeHelper()->getAddress());
		}
		elseif ($dataEntity instanceof DataObject)
		{
			$this->addLink($document, 'type', $dataEntity->getTypeHelper()->getAddress());
		}

		// Object content
		if ($dataEntity instanceof DataCollection)
		{
			$this->serializeCollection($document, $dataEntity);
		}
		else if ($dataEntity instanceof DataObject)
		{
			$this->serializeObject($document, $dataEntity);
		}
		else
		{
			throw InvalidArgumentException::newInvalidType('$dataEntity', $dataEntity);
		}

		return $document;
	}

	private function addLink(\stdClass $document, $rel, $href)
	{
		if (!isset($document->_links))
		{
			$document->_links = new \stdClass;
		}

		$link = new \stdClass;
		$link->href = $href;

		$document->_links->$rel = $link;
	}

	private function addEmbedded(\stdClass $document, $rel, DataEntity $dataEntity, $asList = false)
	{
		if (!isset($document->_embedded))
		{
			$document->_embedded = new \stdclass;
		}

		if ($asList)
		{
			if (!isset($document->_embedded->$rel))
			{
				$document->_embedded->$rel = [$this->serializeDocument($dataEntity)];
			}
			elseif (is_array($document->_embedded->$rel))
			{
				array_push($document->_embedded->$rel, $this->serializeDocument($dataEntity));
			}
			else
			{
				throw new \LogicException;
			}
		}
		else
		{
			$document->_embedded->$rel = $this->serializeDocument($dataEntity);
		}
	}

	private function serializeCollection(\stdClass $document, DataCollection $dataCollection)
	{
		$data = $dataCollection->getData();

		if (is_array($data))	// Collection is a list
		{
			if ($dataCollection->getTypeHelper()->getBaseTypeHelper() instanceof SimpleTypeHelper)
			{
				// Collection contains scalar values
				$document->elements = $data;
			}
			else
			{
				// Collection contains resources
				$elements = array();

				foreach($data as $value)
				{
					if ($value instanceof DataEntity)
					{
						$this->addEmbedded($document, 'elements', $value, true);
					}
					else
					{
						throw new SerializationException('Collection of resources contains scalar values');
					}
				}
			}
		}
		elseif ($data instanceof \stdClass)
		{
			throw new SerializationException('Serialization of dictionaries is not supported');
		}
		else
		{
			throw new \LogicException();
		}
	}

	private function serializeObject(\stdClass $document, DataObject $dataObject)
	{
		foreach($dataObject->getData() as $propertyName => $value)
		{
			if (in_array($propertyName, self::$reservedPropertyNames, true))
			{
				throw new SerializationException("Property '$propertyName' is a reserved name in the HAL format'");
			}

			if ($value instanceof DataObject)
			{
				$this->addEmbedded($document, $propertyName, $value);
			}
			elseif ($value instanceof DataCollection)
			{
				if ($value->getTypeHelper()->getBaseTypeHelper() instanceof SimpleTypeHelper)
				{
					// Collections of simple values are serialized inline.
					 $document->$propertyName = $this->getInlineSimpleCollection($value);
				}
				else
				{
					// Collections of resources are serialized as embedded documents.
					$this->addEmbedded($document, $propertyName, $value);
				}
			}
			else
			{
				$document->$propertyName = $value;
			}
		}
	}

	private function getInlineSimpleCollection(DataCollection $collection)
	{
		if (is_object($collection->getData()))
		{
			throw new SerializationException('Serialization of dictionaries is not supported');
		}

		return $collection->getData();
	}
}