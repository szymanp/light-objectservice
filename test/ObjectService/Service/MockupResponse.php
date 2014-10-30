<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Service\Response\DataEntity;
use Light\ObjectService\Service\Response\Response;

class MockupResponse implements Response
{
	const SEND_ENTITY = "sendEntity";
	const SEND_NEW_ENTITY = "sendNewEntity";
	const SEND_NOT_FOUND = "sendNotFound";

	public $method = null;
	public $result;

	function sendEntity(DataEntity $entity)
	{
		$this->method = self::SEND_ENTITY;
		$this->result = $entity;
	}

	function sendNewEntity($resourcePath, DataEntity $entity)
	{
		$this->method = self::SEND_NEW_ENTITY;
		$this->result = array($resourcePath, $entity);
	}

	function sendNotFound()
	{
		$this->method = self::SEND_NOT_FOUND;
	}

	function sendBadRequest()
	{
		$this->method = "sendBadRequest";
	}

	function sendInternalError(\Exception $e)
	{
		$this->method = "sendInternalError";
		$this->result = $e;
	}

	/**
	 * Returns the content type produced by this Response object.
	 * @return string
	 */
	function getContentType()
	{
		return "php/mockup";
	}

} 