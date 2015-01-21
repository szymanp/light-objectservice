<?php
namespace Light\ObjectService\Resource\Projection;
use Light\ObjectAccess\Resource\Addressing\ResourceAddress;

/**
 * A marker interface for projection result classes.
 */
interface DataEntity
{
	/**
	 * Returns the address associated with this entity.
	 * @return ResourceAddress
	 */
	public function getResourceAddress();
}
