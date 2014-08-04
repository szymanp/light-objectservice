<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Service\Response\DataEntity;
use Light\ObjectService\Service\Response\DataCollection;
use Light\ObjectService\Service\Response\DataObject;
use Light\Exception\InvalidParameterType;

class JsonDataEntitySerializer
{
	/**
	 * Serializes data to JSON.
	 * @param DataEntity $dataEntity
	 * @return mixed	A \stdClass object or an array. 
	 */
	public function serialize(DataEntity $dataEntity)
	{
		if ($dataEntity instanceof DataCollection)
		{
			return $this->serializeCollection($dataEntity);
		}
		else if ($dataEntity instanceof DataObject)
		{
			return $this->serializeObject($dataEntity);
		}
		else
		{
			throw new InvalidParameterType('$dataEntity', $dataEntity);
		}
	}
	
	protected function serializeCollection(DataCollection $collection)
	{
		$result = new \stdClass();
		$meta = $result->meta = new \stdClass();
		
		$meta->rel = $collection->getType()->getUri();
		
		if (is_array($collection->getData()))
		{
			$data = array();
			foreach($collection->getData() as $value)
			{
				if ($value instanceof DataEntity)
				{
					$data[] = $this->serialize($value);
				}
				else
				{
					$data[] = $value;
				}
			}
		}
		else
		{
			$data = new \stdClass();
			foreach($collection->getData() as $key => $value)
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
		
		$result->data = $data;
		
		return $result;
	}
	
	protected function serializeObject(DataObject $object)
	{
		$result = new \stdClass();
		$meta = $result->meta = new \stdClass();
		$data = $result->data = new \stdClass();
		
		$meta->rel = $object->getType()->getUri();
		
		foreach($object->getData() as $propertyName => $value)
		{
			if ($value instanceof DataEntity)
			{
				$data->$propertyName = $this->serialize($value);
			}
			else
			{
				$data->$propertyName = $value;
			}
		}
		
		return $result;
	}
}