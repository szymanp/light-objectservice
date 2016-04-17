<?php
namespace Szyman\ObjectService\Response;

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

	private function addEmbedded(\stdClass $document, $rel, DataEntity $dataEntity)
	{
		if (!isset($document->_embedded))
		{
			$document->_embedded = new \stdclass;
		}

		$document->_embedded->$rel = $this->serializeDocument($dataEntity);
	}

	private function serializeCollection(\stdClass $document, DataCollection $dataCollection)
	{
		$data = $dataCollection->getData();
		$processedData = array();

		$hasScalars = false;
		$hasResources = false;

		// TODO

		if (is_array($data))
		{
			// The collection is a list
			$elements = array();
			foreach($data as $value)
			{
				if ($value instanceof DataEntity)
				{
					$hasResources = true;
					$processedData[] = $this->serializeDocument($value);
				}
				else
				{
					$hasScalars = true;
					$processedData[] = $value;
				}
			}
		}
		elseif ($data instanceof \stdClass)
		{
			// The collection is a dictionary
			$elements = array();
			foreach($data as $key => $value)
			{
				if ($value instanceof DataEntity)
				{
					$hasResources = true;
					$processedData->$key = $this->serializeDocument($value);
				}
				else
				{
					$hasScalars = true;
					$processedData->$key = $value;
				}
			}
		}
		else
		{
			throw new \LogicException();
		}
	}

	protected function serializeObject(\stdClass $document, DataObject $dataObject)
	{
		foreach($dataObject->getData() as $propertyName => $value)
		{
			if (in_array($propertyName, self::$reservedPropertyNames, true))
			{
				throw new SerializationException("Property '$propertyName' is a reserved name in the HAL format'");
			}

			if ($value instanceof DataEntity)
			{
				$this->addEmbedded($document, $propertyName, $value);
			}
			else
			{
				$document->$propertyName = $value;
			}
		}
	}
}