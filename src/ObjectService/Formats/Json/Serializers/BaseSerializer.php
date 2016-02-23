<?php
namespace Light\ObjectService\Formats\Json\Serializers;

use Szyman\Exception\InvalidArgumentException;
use Light\ObjectService\Service\Protocol\Serializer;

abstract class BaseSerializer implements Serializer
{
	/** @var string */
	private $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
		if (!is_string($contentType))
		{
			throw InvalidArgumentException::newInvalidType('$contentType', $contentType, "string");
		}
	}

	/**
	 * Returns the content type produced by this serializer.
	 * @return string
	 */
	final public function getContentType()
	{
		return $this->contentType;
	}
}