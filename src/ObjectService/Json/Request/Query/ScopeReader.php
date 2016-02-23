<?php
namespace Light\ObjectService\Json\Request\Query;

use Szyman\Exception\NotImplementedException;
use Light\ObjectAccess\Query\Scope;
use Light\ObjectService\Exception\MalformedRequest;
use Light\ObjectService\Json\Request\Reader;

class ScopeReader extends Reader
{
	/**
	 * Reads a Scope object from a json value.
	 * @param $json
	 * @return Scope
	 */
	public function read($json)
	{
		if (is_object($json))
		{
			if (isset($json->key))
			{
				if (is_array($json->key))
				{
					// TODO This also involves changes to Scope itself
					throw new NotImplementedException("Multiple keys in scope");
				}
				else
				{
					return Scope::createWithKey($json->key);
				}
			}
			elseif (isset($json->offset))
			{
				if (is_array($json->offset))
				{
					// TODO This also involves changes to Scope itself
					throw new NotImplementedException("Multiple offsets in scope");
				}
				else
				{
					return Scope::createWithIndex($json->offset);
				}
			}
			elseif (isset($json->value))
			{
				// TODO
			}
			elseif (isset($json->query))
			{
				// TODO
			}
			else
			{
				throw new MalformedRequest("Invalid scope specification");
			}
		}
	}
}