<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Exception\SerializationException;
use Light\ObjectService\Resource\Projection\DataEntity;

interface StructureSerializer
{
	/**
	 * Serializes a <kbd>DataEntity</kbd> object structure to an intermediate primitive PHP object hierarchy.
	 * @param DataEntity $dataEntity
	 * @return mixed
	 * @throws SerializationException	Thrown if there is a problem serializing the data.
	 */
	public function serializeStructure(DataEntity $dataEntity);
}
