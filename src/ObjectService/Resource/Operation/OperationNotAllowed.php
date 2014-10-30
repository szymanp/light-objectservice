<?php
namespace Light\ObjectService\Resource\Operation;

use Light\Exception\Exception;
use Light\ObjectService\Resource\ResolvedValue;

class OperationNotAllowed extends Exception
{
	public function __construct(ResolvedValue $resource, $reason)
	{
		try
		{
			$url = $resource->getEndpointUrl()->getUrl();
		}
		catch (\Exception $e)
		{
			$url = "(URL not available)";
		}

		parent::__construct("%1: %2", $reason, $url);
	}
}