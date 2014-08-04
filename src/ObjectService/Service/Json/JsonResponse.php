<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Service\Response\Response;
use Light\ObjectService\Service\Response\DataEntity;
use Light\Util\HTTP\Response as HTTPResponse;
use Light\Exception\NotImplementedException;

class JsonResponse implements Response
{
	/**
	 * The HTTP response object to use.
	 * @var \Light\Util\HTTP\Response
	 */
	private $httpResponse;
	
	public function __construct(HTTPResponse $response)
	{
		$this->httpResponse = $response;
	}
	
	public function sendEntity(DataEntity $entity)
	{
		$exception = null;
		
		try
		{
			$body = $this->serializeEntity($entity);
		}
		catch (\Exception $e)
		{
			$exception = $e;
		}
		
		if (!$exception)
		{
			$this->httpResponse->sendStatus(200);
			$this->httpResponse->setHeader("Content-type", "application/json", true);
			$this->httpResponse->sendBody($body);
		}
		else
		{
			$this->sendInternalError($exception);
		}
	}
	
	public function sendNewEntity($resourcePath, DataEntity $entity)
	{
		$exception = null;
		
		try
		{
			$body = $this->serializeEntity($entity);
		}
		catch (\Exception $e)
		{
			$exception = $e;
		}
		
		if (!$exception)
		{
			$this->httpResponse->sendStatus(201);
			$this->httpResponse->setHeader("Content-type", "application/json", true);
			$this->httpResponse->setHeader("Location", $resourcePath, true);
			$this->httpResponse->sendBody($body);
		}
		else
		{
			$this->sendInternalError($exception);
		}
	}
	
	public function sendNotFound()
	{
		throw new NotImplementedException();
	}
	
	public function sendBadRequest()
	{
		throw new NotImplementedException();
	}
	
	public function sendInternalError(\Exception $e)
	{
		throw new NotImplementedException();
	}
	
	/**
	 * @param DataEntity $entity
	 * @return string
	 */
	private function serializeEntity(DataEntity $entity)
	{
		$serializer = new JsonDataEntitySerializer();
		$rawObject = $serializer->serialize($entity);
		return json_encode($rawObject);
	}
}