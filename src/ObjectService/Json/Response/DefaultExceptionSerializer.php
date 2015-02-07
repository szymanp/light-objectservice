<?php
namespace Light\ObjectService\Json\Response;

use Light\ObjectService\Service\Protocol\ExceptionSerializer;

class DefaultExceptionSerializer implements ExceptionSerializer
{
	/** @var bool */
	protected $detailed;

	public function __construct($detailed = false)
	{
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

	/**
	 * Returns the content type produced by this serializer.
	 * @return string
	 */
	public function getContentType()
	{
		return "text/json";
	}

}