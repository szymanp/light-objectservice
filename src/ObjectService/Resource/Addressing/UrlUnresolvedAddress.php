<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectAccess\Resource\Addressing\Address;
use Szyman\Exception\InvalidArgumentException;

/**
 * A 'raw' fully-qualified URL string that has not been resolved to a resource.
 */
final class UrlUnresolvedAddress implements Address
{
	/** @var string */
	private $url;

	public function __construct($url)
	{
		if (!is_string($url)) throw InvalidArgumentException::newInvalidType('$url', $url, 'string');
		if (empty($url)) throw InvalidArgumentException::newInvalidValue('$url', $url);

		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->url;
	}
}