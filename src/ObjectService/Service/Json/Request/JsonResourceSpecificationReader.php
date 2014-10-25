<?php
namespace Light\ObjectService\Service\Json;
use Light\ObjectService\Exceptions\ResolutionException;
use Light\ObjectService\Resource\Operation\UpdateOperation;

/**
 * Reads a ResourceSpecification
 */
final class JsonResourceSpecificationReader
{
	const SPEC_NEW	= "new";
	const SPEC_MODIFY = "modify";
	const SPEC_REFERENCE = "reference";

	public function __construct(\stdClass $meta = null, \stdClass $data = null)
	{
		$spec = $this->determineSpec($meta, $data);

		switch ($spec)
		{
			case self::SPEC_NEW:
				return $this->readNew($meta, $data);
			case self::SPEC_MODIFY:
				return $this->readModify($meta, $data);
			case self::SPEC_REFERENCE:
				return $this->readReference($meta, $data);
		}
	}

	private function determineSpec($meta, $data)
	{
		if ($meta && isset($meta->spec))
		{
			return $meta->spec;
		}
		else if ($meta && isset($meta->href))
		{
			return self::SPEC_REFERENCE;
		}
		else if ($meta && isset($meta->type))
		{
			return self::SPEC_NEW;
		}
		else if ($data && !$meta)
		{
			return self::SPEC_MODIFY;
		}
		else
		{
			throw new ResolutionException("Cannot determine specification type");
		}
	}

	private function readNew($meta, $data)
	{
		$type = $meta->type;

	}

	/**
	 * @param $meta
	 * @param $data
	 * @return UpdateOperation
	 */
	private function readModify($meta, $data)
	{

	}

	/**
	 * @param $meta
	 * @param $data
	 */
	private function readReference($meta, $data)
	{

	}
}