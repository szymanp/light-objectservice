<?php
namespace Light\ObjectService\Resource\Util;

use Light\ObjectAccess\Resource\Addressing\ResourceAddress;
use Light\ObjectService\Resource\Projection\DataCollection;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Resource\Projection\DataObject;

/**
 * Prints a hierarchy of DataEntity objects as a string.
 */
class DataEntityPrinter
{
	/**
	 * @param DataEntity $value
	 * @return string
	 */
	public static function getPrintout(DataEntity $value)
	{
		$printer = new self;
		return $printer->printValue($value);
	}

	public function printValue(DataEntity $value)
	{
		if ($value instanceof DataObject)
		{
			return $this->printObject($value);
		}
		else
		{
			return $this->printCollection($value);
		}
	}

	protected function printObject(DataObject $object)
	{
		$addr = $this->getAddress($object->getResourceAddress());
		$result = $object->getTypeHelper()->getName() . " @ " . $addr . " {";

		$isEmpty = true;

		foreach ($object->getData() as $key => $value)
		{
			$isEmpty = false;

			$result .= "\n  $key: ";
			if (is_scalar($value))
			{
				$result .= $this->indent((string)$value);
			}
			else
			{
				$result .= $this->indent($this->printValue($value));
			}
		}

		$result .= $isEmpty ? "}" : "\n}";

		return $result;
	}

	protected function printCollection(DataCollection $collection)
	{
		$addr = $this->getAddress($collection->getResourceAddress());
		$result = $collection->getTypeHelper()->getName() . " @ " . $addr . " [";

		foreach($collection->getData() as $key => $value)
		{
			$result .= "\n  $key: ";
			if (is_scalar($value))
			{
				$result .= $this->indent((string) $value);
			}
			else
			{
				$result .= $this->indent($this->printValue($value));
			}
		}

		$result .= "\n]";
		return $result;
	}

	private function getAddress(ResourceAddress $address)
	{
		return $address->hasStringForm() ? $address->getAsString() : "-";
	}

	private function indent($text)
	{
		return str_replace("\n", "\n  ", $text);
	}
}