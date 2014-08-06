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
	
	/**
	 * @var \Light\ObjectService\Service\Json\JsonResponseParameters
	 */
	private $responseParameters;
	
	public function __construct(HTTPResponse $response, JsonResponseParameters $params = null)
	{
		$this->httpResponse = $response;
		
		if (!$params)
		{
			$params = new JsonResponseParameters();
		}
		$this->responseParameters = $params;
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
		$this->httpResponse->sendStatus(500);
		$this->sendException($e);
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Response\Response::getContentType()
	 */
	public function getContentType()
	{
		return "application/json";
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
	
	private function sendException(\Exception $e)
	{
		$this->httpResponse->setHeader("Content-type", "application/json", true);
		$this->httpResponse->sendBody(json_encode($this->serializeException($e)));
	}
	
	private function serializeException(\Exception $e)
	{
		$data = new \stdClass();
		$data->code 	= $e->getCode();
		$data->message	= $e->getMessage();
		$data->class	= get_class($e);
		$data->file		= $e->getFile();
		$data->line		= $e->getLine();
		$data->trace	= $e->getTrace();
		if ($e->getPrevious())
		{
			$data->previous = $this->serializeException($e->getPrevious());
		}
		
		return $data;
	}
}