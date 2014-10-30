<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Expression\ParsedRootPathExpression;
use Light\ObjectService\Resource\Operation\CreateOperation;
use Light\ObjectService\Resource\Operation\Operation;
use Light\ObjectService\Resource\Operation\UpdateOperation;
use Light\ObjectService\Service\Request\Request;
use Light\ObjectService\Service\Response\Projector;
use Light\ObjectService\Service\Response\Response;
use Light\ObjectService\Type\PathReader;

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

		$resolvedResourceIdentifier = $this->request->getResourceIdentifier()->resolve($this->conf->getEndpointRegistry());
		$pathReader = new PathReader($resolvedResourceIdentifier->getResourcePath(), $resolvedResourceIdentifier->getEndpoint()->getObjectRegistry());
		if ($this->request->getSelection())
		{
			$pathReader->setTargetSelection($this->request->getSelection());
		}
		$resource = $pathReader->read();
		$resultResource = null;

		$operations = $this->getPrioritizedOperations($this->request->getOperations());
		foreach($operations as $operation)
		{
			$resultResource = $operation->execute($resource, $this->conf);
		}

		if (is_null($resultResource))
		{
			$resultResource = $resource;
		}

		$projector = Projector::create($resolvedResourceIdentifier->getEndpoint()->getObjectRegistry(), $resultResource->getType());
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

	private function getPrioritizedOperations(array $operations)
	{
		$orderingFn = function(Operation $operation)
		{
			if ($operation instanceof UpdateOperation)
			{
				return 1;
			}
			else
			{
				return 2;
			}
		};

		$comparatorFn = function(Operation $a, Operation $b) use ($orderingFn)
		{
			$pa = $orderingFn($a);
			$pb = $orderingFn($b);
			if ($pa == $pb)
			{
				return 0;
			}
			else if ($pa > $pb)
			{
				return 1;
			}
			else
			{
				return -1;
			}
		};

		usort($operations, $comparatorFn);

		return $operations;
	}
}