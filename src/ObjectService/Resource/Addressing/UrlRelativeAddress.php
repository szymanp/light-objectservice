<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectAccess\Resource\Util\DefaultRelativeAddress;

class UrlRelativeAddress extends DefaultRelativeAddress
{
	/**
	 * Appends an address fragment to this address.
	 * @param string	$addressFragment	A fragment of an address. For example: "path/to/somewhere"
	 * @return $this
	 */
	public function appendFragment($addressFragment)
	{
		$parts = explode("/", $addressFragment);
		foreach($parts as $part)
		{
			$this->appendElement($part);
		}
		return $this;
	}
}