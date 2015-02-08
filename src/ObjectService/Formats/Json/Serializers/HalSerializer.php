<?php
namespace Light\ObjectService\Formats\Json\Serializers;

use Light\Exception\InvalidParameterType;
use Light\ObjectService\Resource\Projection\DataCollection;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Resource\Projection\DataObject;
use Light\ObjectService\Service\Protocol\ResourceSerializer;

/**
 * Hypertext Application Language serializer.
 *
 * This class serializes objects using the HAL format, as described in
 * https://tools.ietf.org/html/draft-kelly-json-hal-06
 *
 */
class HalSerializer implements ResourceSerializer
{
	/** @var \stdClass */
	private $document;
	/** @var string */
	private $contentType;

	public function __construct($contentType = "application/hal+json")
	{
		$this->contentType = $contentType;
	}

	/**
	 * @inheritdoc
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
		$this->document = new \stdClass;

		if ($dataEntity->getResourceAddress()->hasStringForm())
		{
			$this->addLink("self", $dataEntity->getResourceAddress()->getAsString());
		}

		if ($dataEntity instanceof DataCollection)
		{
			$this->serializeCollection($dataEntity);
		}
		else if ($dataEntity instanceof DataObject)
		{
			$this->serializeObject($dataEntity);
		}
		else
		{
			throw new InvalidParameterType('$dataEntity', $dataEntity);
		}

		return $this->document;
	}

	/**
	 * @inheritdoc
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	protected function addLink($rel, $href)
	{
		if (!isset($this->document->_links))
		{
			$this->document->_links = new \stdclass;
		}

		$link = new \stdClass;
		$link->href = $href;

		$this->document->_links->$rel = $link;
	}

	protected function addEmbedded($rel, DataEntity $dataEntity)
	{
		if (!isset($this->document->_embedded))
		{
			$this->document->_embedded = new \stdclass;
		}

		// TODO

	}

	protected function serializeCollection(DataCollection $dataCollection)
	{
		$data = $dataCollection->getData();

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
					$hasScalars = true;
					$data[] = $this->serialize($value);
				}
				else
				{
					$hasResources = true;
					$data[] = $value;
				}
			}
		}
		else
		{
			// The collection is a dictionary
			$elements = array();
			foreach($data as $key => $value)
			{
				if ($value instanceof DataEntity)
				{
					$data->$key = $this->serialize($value);
				}
				else
				{
					$data->$key = $value;
				}
			}
		}
	}

	protected function serializeObject(DataObject $dataObject)
	{
		foreach($dataObject->getData() as $propertyName => $value)
		{
			if ($value instanceof DataEntity)
			{
				$this->addEmbedded($propertyName, $value);
			}
			else
			{
				$this->document->$propertyName = $value;
			}
		}
	}
}