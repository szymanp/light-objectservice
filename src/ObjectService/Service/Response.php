<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Resource\Projection\DataEntity;

/**
 * An interface for classes capable of responding to a service request.
 */
interface Response
{
	public function sendEntity(DataEntity $entity, $isNewEntity);

	public function sendException(\Exception $e);
}