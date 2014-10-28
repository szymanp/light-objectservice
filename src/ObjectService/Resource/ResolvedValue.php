<?php
namespace Light\ObjectService\Resource;

use Light\ObjectService\Resource\Addressing\EndpointUrl;
use Light\ObjectService\Type\Type;

/**
 * A value read from a resource path.
 *
 */
final class ResolvedValue
{
	/** @var mixed */
	private $value;
	
	/** @var \Light\ObjectService\Type\Type */
	private $type;

	/** @var EndpointUrl */
	private $url;
	
	public function __construct(Type $type, $value, EndpointUrl $url = null)
	{
		$this->value = $value;
		$this->type	 = $type;
		$this->url   = $url;
	}

	/**
	 * Returns the value.
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Returns the type of the value.
	 * @return \Light\ObjectService\Type\Type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return EndpointUrl
	 */
	public function getEndpointUrl()
	{
		// TODO
		// $this->url could be NULL. If it is, we should ask the type to compute a standard URL.
		return $this->url;
	}

}