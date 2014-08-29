<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Service\Request\Request;
use Light\ObjectService\Service\Response\Response;
use Light\ObjectService\Type\PathReader;
use Light\ObjectService\Expression\ParsedPathExpression;
use Light\ObjectService\Expression\ParsedRootPathExpression;
use Light\ObjectService\Resource\Operation\CreateOperation;
use Light\ObjectService\Service\Response\Projector;

class Invocation
{
	/** @var InvocationParameters */
	private $conf;
	/** @var \Light\ObjectService\Service\Request\Request */
	private $request;
	/** @var \Light\ObjectService\Service\Response\Response */
	private $response;
	
	public function __construct(InvocationParameters $conf, Request $request, Response $response)
	{
		$this->conf = $conf;
		$this->request = $request;
		$this->response = $response;
	}
	
	public function invoke()
	{
		// TODO exception handling
		
		$resourcePath = new ParsedRootPathExpression($this->request->getResourcePath(), $this->conf->getObjectRegistry());
		$pathReader = new PathReader($resourcePath, $this->conf->getObjectRegistry());
		if ($this->request->getSelection())
		{
			$pathReader->setTargetSelection($this->request->getSelection());
		}
		$resource = $pathReader->read();
		
		$operation = $this->request->getOperation();
		$operation->setResource($resource);

		$operation->execute($this->conf);
		
		if ($operation instanceof CreateOperation)
		{
			$resultResource = $operation->getNewResource();
		}
		else
		{
			$resultResource = $operation->getResource();
		}
		
		$projector = Projector::create($this->conf->getObjectRegistry(), $resultResource->getType());
		if ($this->request->getSelection())
		{
			$selection = $this->request->getSelection()->compile($resultResource->getType()); 
		}
		else
		{
			$selection = null;
		}
		$projectedResultResource = $projector->project($resultResource->getValue(), $selection);
		
		if ($operation instanceof CreateOperation)
		{
			$this->response->sendNewEntity($resultResource->getPath()->getPath(), $projectedResultResource);
		}
		else
		{
			$this->response->sendEntity($projectedResultResource);
		}
	}
}