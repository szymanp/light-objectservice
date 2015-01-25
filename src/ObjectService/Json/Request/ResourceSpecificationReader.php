<?php
namespace Light\ObjectService\Json\Request;

use Light\ObjectService\Exception\MalformedRequest;
use Light\ObjectService\Json\Request\Operation\UpdateOperationReader;
use Light\ObjectService\Resource\ExistingResourceSpecification;
use Light\ObjectService\Resource\NewResourceSpecification;
use Light\ObjectService\Resource\ResourceSpecification;

class ResourceSpecificationReader extends Reader
{
	/**
	 * Reads a resource specification from a JSON object.
	 * @param \stdClass $json
	 * @return ResourceSpecification
	 */
	public function read(\stdClass $json)
	{
		$meta = new ResourceSpecificationReader_Meta();

		if (isset($json->meta))
		{
			$meta->read($json->meta);
		}
		else
		{
			throw new MalformedRequest("A request specification must have a \"meta\" attribute");
		}

		if ($meta->spec == ResourceSpecificationReader_Meta::SPEC_NEW)
		{
			$typeHelper = $this->getExecutionParameters()->getEndpoint()->getTypeRegistry()->getTypeHelperByUri($meta->type);
			if (isset($json->data))
			{
				$updateOperationReader = new UpdateOperationReader($this->getExecutionParameters());
				$updateOperation = $updateOperationReader->read($json->data);
			}
			else
			{
				$updateOperation = null;
			}
			return new NewResourceSpecification($typeHelper, $updateOperation);
		}
		elseif ($meta->spec == ResourceSpecificationReader_Meta::SPEC_REF)
		{
			return new ExistingResourceSpecification($this->executionParameters->getEndpointRegistry()->getResourceAddress($meta->href));
		}
		else
		{
			throw new MalformedRequest("Invalid value for meta.type = \"%1\"", $json->meta->type);
		}
	}
}

class ResourceSpecificationReader_Meta
{
	const SPEC_NEW = "new";
	const SPEC_REF = "reference";

	/**
	 * Either "new" or "reference"
	 * @var string
	 */
	public $spec;
	/**
	 * If spec == "reference", then this is the URL of the referenced resource
	 * @var string
	 */
	public $href;
	/**
	 * If spec == "type", then this is the URL of the type for the new resource.
	 * @var string
	 */
	public $type;

	public function read(\stdClass $meta)
	{
		if (isset($meta->spec))
		{
			$this->spec = strtolower(trim($meta->spec));
		}
		if (isset($meta->href))
		{
			$this->href = $meta->href;
			if (empty($meta->spec))
			{
				$meta->spec = self::SPEC_REF;
			}
		}
		if (isset($meta->type))
		{
			$this->type = $meta->type;
			if (empty($meta->spec))
			{
				$meta->spec = self::SPEC_NEW;
			}
		}

		if (empty($this->spec))
		{
			if (!empty($this->href) && !empty($this->type))
			{
				throw new MalformedRequest("A resource specification \"meta\" part cannot both have a \"href\" and \"type\" attribute");
			}
			elseif (!empty($this->type))
			{
				$this->spec = self::SPEC_NEW;
			}
			elseif (!empty($this->href))
			{
				$this->spec = self::SPEC_REF;
			}
			else
			{
				throw new MalformedRequest("A resource specification \"meta\" part must have one of the following attributes: \"href\" or \"type\"");
			}
		}
	}
}