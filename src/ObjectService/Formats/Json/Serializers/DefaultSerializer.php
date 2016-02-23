<?php
namespace Light\ObjectService\Formats\Json\Serializers;

use Light\ObjectService\Resource\Projection\DataCollection;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Resource\Projection\DataObject;
use Light\ObjectService\Service\Protocol\ResourceSerializer;
use Szyman\Exception\InvalidArgumentException;

/**
 * Default serializer
 */
class DefaultSerializer extends BaseSerializer implements ResourceSerializer
{
	public function __construct($contentType = "application/json")
	{
		parent::__construct($contentType);
	}

	/**
	 * Serializes a projected resource.
	 * @param DataEntity $dataEntity
	 * @return mixed
	 */
	public function serialize(DataEntity $dataEntity)
	{
		return json_encode($this->serializeToObject($dataEntity));
	}

	/**
	 * @param DataEntity $dataEntity
	 * @return \stdClass
	 */
	public function serializeToObject(DataEntity $dataEntity)
	{
		$document = new \stdClass();

		if ($dataEntity->getResourceAddress()->hasStringForm())
		{
			$this->addLink($document, "self", $dataEntity->getResourceAddress()->getAsString());
		}

		if ($dataEntity instanceof DataCollection)
		{
			$document->data = $this->serializeCollection($dataEntity);
		}
		else if ($dataEntity instanceof DataObject)
		{
			$document->data = $this->serializeObject($dataEntity);
		}
		else
		{
			throw InvalidArgumentException::newInvalidType('$dataEntity', $dataEntity);
		}

		return $document;
	}

	protected function addLink(\stdClass $document, $rel, $href)
	{
		if (!isset($document->links))
		{
			$document->links = new \stdclass;
		}

		$link = new \stdClass;
		$link->href = $href;

		$document->links->$rel = $link;
	}

	protected function serializeCollection(DataCollection $dataCollection)
	{
		$data = $dataCollection->getData();

		if (is_array($data))
		{
			// The collection is a list
			$elements = array();
			foreach($data as $value)
			{
				if ($value instanceof DataEntity)
				{
					$elements[] = $this->serializeToObject($value);
				}
				else
				{
					$elements[] = $value;
				}
			}
			return $elements;
		}
		else
		{
			// The collection is a dictionary
			$elements = new \stdClass();
			foreach($data as $key => $value)
			{
				if ($value instanceof DataEntity)
				{
					$elements->$key = $this->serializeToObject($value);
				}
				else
				{
					$elements->$key = $value;
				}
			}
			return $elements;
		}
	}

	protected function serializeObject(DataObject $dataObject)
	{
		$data = new \stdClass();

		foreach($dataObject->getData() as $propertyName => $value)
		{
			if ($value instanceof DataEntity)
			{
				$data->$propertyName = $this->serializeToObject($value);
			}
			else
			{
				$data->$propertyName = $value;
			}
		}

		return $data;
	}
}