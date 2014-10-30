<?php
namespace Light\ObjectService\Resource;

use Light\Exception\Exception;
use Light\ObjectService\Resource\Addressing\EndpointUrl;
use Light\ObjectService\Type\CollectionType;
use Light\ObjectService\Type\ObjectProvider;
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

	/**
	 * Returns a new ResolvedValue object referring to an unresolved collection (i.e. an ObjectProvider object).
	 * @param ObjectProvider $type
	 * @param EndpointUrl    $url
	 * @return ResolvedValue
	 */
	public static function unresolvedCollection(ObjectProvider $type, EndpointUrl $url = null)
	{
		// TODO Note that the URL for this might be null.
		// We cannot obtain a standard URL in this case, which basically means that there
		// might be cases when no URL is available. getEndpointUrl() should then throw an exception.

		return new self($type, $type, $url);
	}
	
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
	 * Returns a list of ResolvedValues for the elements in this collection.
	 * @throws Exception	If this resource is not a resolved collection of values.
	 * @return ResolvedValue[]
	 */
	public function getValueAsResources()
	{
		if ($this->isCollection() &&
			(is_array($this->value) || $this->value instanceof \Iterator))
		{
			$resources = array();
			foreach ($this->value as $value)
			{
				$resources[] = new self($this->type->getBaseType(), $value);
			}
			return $resources;
		}
		else
		{
			throw new Exception("This resource does not contain a resolved collection");
		}
	}

	/**
	 * Returns true if this resource represents a collection.
	 * @return bool
	 */
	public function isCollection()
	{
		return $this->type instanceof CollectionType;
	}

	/**
	 * Returns true if this resource represents an unresolved collection.
	 * @return bool
	 */
	public function isUnresolvedCollection()
	{
		return $this->type instanceof ObjectProvider
			   && $this->type === $this->value;
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