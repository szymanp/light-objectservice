<?php
namespace Light\ObjectService\Formats\Json\Serializers;

use Light\ObjectService\Service\Protocol\ExceptionSerializer;

class DefaultExceptionSerializer extends BaseSerializer implements ExceptionSerializer
{
	/** @var bool */
	protected $detailed;

	public function __construct($detailed = false, $contentType = "application/json")
	{
		parent::__construct($contentType);
		$this->detailed = $detailed;
	}

	/**
	 * Serializes an exception.
	 * @param \Exception $e
	 * @return string
	 */
	public function serialize(\Exception $e)
	{
		return json_encode($this->serializeToObject($e));
	}

	public function serializeToObject(\Exception $e)
	{
		$json = new \stdClass();
		$json->exceptionClass = get_class($e);
		$json->message = $e->getMessage();
		if ($previous = $e->getPrevious())
		{
			$json->previous = $this->serializeToObject($previous);
		}
		return $json;
	}
}