<?php
namespace Light\ObjectService\Json\Request\Operation;

use Light\ObjectService\Json\Request\Reader;
use Light\ObjectService\Resource\Operation\DeleteOperation;

class DeleteOperationReader extends Reader
{
	public function read(\stdclass $json)
	{
		$deleteOperation = new DeleteOperation();

		// TODO

		return $deleteOperation;
	}
}