<?php
namespace Light\ObjectService\Service\Protocol;

use Light\ObjectService\Resource\Projection\DataEntity;

interface ResourceSerializer extends Serializer
{
	/**
	 * Serializes a projected resource.
	 * @param DataEntity $dataEntity
	 * @return mixed
	 */
	public function serialize(DataEntity $dataEntity);
}