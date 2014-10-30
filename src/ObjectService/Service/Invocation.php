<?php
namespace Light\ObjectService\Service;

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

		// TODO
		// The resource might be a collection of values. If so, the operation(s) should be executed
		// on each of the resources separately. We need to modify ResolvedValue to contain information
		// about the state of the collection (not a collection | unresolved collection | collection values).
		// PathReader should then also be modified to feed this information into ResolvedValue.

		$resultResource = null;

		$operations = $this->getPrioritizedOperations($this->request->getOperations());

		// Execute the requested operation(s) on the relevant resource(s).
		if ($resource->isCollection() && !$resource->isUnresolvedCollection())
		{
			// Execute the operations on all resources in the collection.
			$resultResource = $resource;

			foreach($resource->getValueAsResources() as $value)
			{
				foreach($operations as $operation)
				{
					$operation->execute($value, $this->conf);
				}
			}
		}
		else
		{
			// Execute the operation on the requested resource only.
			foreach($operations as $operation)
			{
				// TODO
				// As we might be invoking many operations, we should probably pick
				// the resource from the first executed operation as the result resource.
				$resultResource = $operation->execute($resource, $this->conf);
			}
		}

		if (is_null($resultResource))
		{
			$resultResource = $resource;
		}

		// TODO
		// Some resources (in particular those that are isUnresolvedCollection) do not have any value to be projected.
		// You could argue that we should give some kind of Method not allowed error when reading them, but projection
		// will happen even if you are appending to such a resource.
		// So what are the options?
		// - return nothing
		// - invent some information to be returned (e.g. number of elements in collection, etc.)
		// - find an alternative resource to show (e.g. if we were appending, then the one that was appended);
		//	 but what if no alternative resource can be found - e.g. when we were sorting
		// - evaluate the collection to return its elements

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

		// TODO
		// We do not have a Create operation anymore - but we have a NewResourceSpecification.
		// How to pick up from Operation that a new resource could have been created there?

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