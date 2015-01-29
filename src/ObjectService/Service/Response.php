<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Resource\Projection\DataEntity;

/**
 * An interface for classes capable of responding to a service request.
 */
interface Response
{
	public function setOperations($operations = array());

	public function setEntity(DataEntity $entity);

	public function setException(\Exception $e);

	public function setScalarValue($value);

	public function send();
}