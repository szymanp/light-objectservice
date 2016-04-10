<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Resource\Projection\DataEntity;

interface StructureSerializer
{
	/**
	 * Serializes a <kbd>DataEntity</kbd> object structure to an intermediate primitive PHP object hierarchy.
	 * @param DataEntity $dataEntity
	 * @return \stdClass
	 * TODO What does it throw?
	 */
	public function serializeStructure(DataEntity $dataEntity);
}