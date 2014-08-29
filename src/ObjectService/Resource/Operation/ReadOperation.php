<?php
namespace Light\ObjectService\Resource\Operation;

/**
 * A Read operation does not alter the resource. 
 */
final class ReadOperation extends Operation
{
	public function execute(ExecutionParameters $params)
	{
		if (!$this->getResource())
		{
			$this->setResource($this->readResource($params));
		}
	}
}