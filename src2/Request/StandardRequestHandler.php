<?php
namespace Szyman\ObjectService\Request;

use Light\ObjectAccess\Resource\ResolvedCollection;
use Light\ObjectAccess\Type\CollectionTypeHelper;
use Light\ObjectAccess\Type\SimpleType;
use Symfony\Component\HttpFoundation\Request;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Type\ComplexType;
use Light\ObjectAccess\Type\CollectionType;
use Szyman\ObjectService\Service\CollectionValueModification;
use Szyman\ObjectService\Service\ComplexValueRepresentation;
use Szyman\ObjectService\Service\ComplexValueModification;
use Szyman\ObjectService\Service\DeserializedBody;
use Szyman\ObjectService\Service\RequestComponents;
use Szyman\ObjectService\Service\RequestHandler;
use Szyman\ObjectService\Service\RequestType;
use Szyman\ObjectService\Service\RequestResult;
use Szyman\ObjectService\Service\ResourceRequestResult;
use Szyman\ObjectService\Service\ExecutionEnvironment;
use Szyman\Exception\NotImplementedException;

class StandardRequestHandler implements RequestHandler
{
	/** @var ExecutionEnvironment */
	protected $env;

	public function __construct(ExecutionEnvironment $env)
	{
		$this->env = $env;
	}

	/**
	 * Handle a request.
	 *
	 * @param Request			$request			The HTTP request object.
	 * @param RequestComponents	$requestComponents
	 * @return RequestResult
	 */
	final public function handle(Request $request, RequestComponents $requestComponents)
	{
		switch($requestComponents->getRequestType()->getValue())
		{
			case RequestType::READ:
				return $this->handleRead($request, $requestComponents);
				
			case RequestType::CREATE:
				return $this->handleCreate($request, $requestComponents);
				
			case RequestType::MODIFY:
				return $this->handleModify($request, $requestComponents);
			
			case RequestType::REPLACE:
				throw new NotImplementedException;	// TODO
				
			case RequestType::DELETE:
				throw new NotImplementedException;	// TODO

			case RequestType::ACTION:
				throw new NotImplementedException;	// TODO
			
			default:
				throw new \LogicException("Invalid request type");
		}
	}
	
	/**
	 * Deserialize the request body.
	 * @param Request			$request			The HTTP request object.
	 * @param RequestComponents	$requestComponents
	 * @return DeserializedBody
	 */
	final protected function deserialize(Request $request, RequestComponents $requestComponents)
	{
		$deserializer = $requestComponents->getDeserializer();
		
		if (is_null($deserializer))
		{
			throw new \LogicException("No deserializer found");
		}
		
		return $deserializer->deserialize($request->getContent());
	}
	
	/**
	 * Handle a Read request.
	 * @param Request			$request			The HTTP request object.
	 * @param RequestComponents	$requestComponents
	 * @return RequestResult
	 */
	protected function handleRead(Request $request, RequestComponents $requestComponents)
	{
		return new ResourceRequestResult($requestComponents->getSubjectResource());
	}

	/**
	 * Handle a Create request.
	 * @param Request			$request			The HTTP request object.
	 * @param RequestComponents	$requestComponents
	 * @return RequestResult
	 */
	protected function handleCreate(Request $request, RequestComponents $requestComponents)
	{
		$body = $this->deserialize($request, $requestComponents);
		$subject = $requestComponents->getSubjectResource();
		$subjectTypeHelper = $subject->getTypeHelper();
		
		// For 'create' requests, the subject resource is always a collection.
		if (!($subjectTypeHelper instanceof CollectionTypeHelper))
		{
			throw new \LogicException('Subject resource is not a collection');
		}
		
		// Create the collection element.
		$elementTypeHelper = $subjectTypeHelper->getBaseTypeHelper();
		if ($elementTypeHelper->getType() instanceof ComplexType)
		{
			$newElement = $elementTypeHelper->createResource($this->env->getTransaction());
		}
		elseif ($elementTypeHelper->getType() instanceof SimpleType)
		{
			throw new NotImplementedException;	// TODO
		}
		elseif ($elementTypeHelper->getType() instanceof CollectionType)
		{
			throw new NotImplementedException;	// TODO
		}
		else
		{
			throw new \LogicException('Unknown helper type');
		}
		
		// Apply the deserialized representation to the element.
		if ($newElement instanceof ResolvedObject && $body instanceof ComplexValueModification)
		{
			// ComplexValueModification is not really applicable to Create requests.
			// However, if we happen to have a DeserializedBody of that type, we can simply use updateObject
			// instead of ComplexValueRepresentation::replaceObject, as we know that the element was just created
			// and there is no need to nullify all the field values.
			$body->updateObject($newElement, $this->env);
		}
		elseif ($newElement instanceof ResolvedObject && $body instanceof ComplexValueRepresentation)
		{
			$body->replaceObject($newElement, $this->env);
		}
		else
		{
			// TODO We also need to implement code for other representations.
			throw new \LogicException('Invalid combination of representation and resource type');
		}

		// Append the element to the collection.
		if (!is_null($reladdr = $requestComponents->getRelativeAddress()))
		{
			// The new element should be appended at a specific key in the collection.
			if (count($reladdr->getPathElements()) != 1)
			{
				throw new \LogicException('The relative address to the target resource must consist of a single path element only');
			}
			
			$key = $reladdr->getPathElements()[0];
			$newElement = $subjectTypeHelper->setValue($subject, $key, $newElement->getValue(), $this->env->getTransaction());
		}
		else
		{
			// The new element should be appended at an arbitrary position.
			$newElement = $subjectTypeHelper->appendValue($subject, $newElement->getValue(), $this->env->getTransaction());
		}

		return new ResourceRequestResult($newElement);
	}

	/**
	 * Handle a Modify request.
	 * @param Request			$request			The HTTP request object.
	 * @param RequestComponents	$requestComponents
	 * @return RequestResult
	 */
	protected function handleModify(Request $request, RequestComponents $requestComponents)
	{
		$body = $this->deserialize($request, $requestComponents);
		$subject = $requestComponents->getSubjectResource();

		if ($subject instanceof ResolvedObject && $body instanceof ComplexValueModification)
		{
			$body->updateObject($subject, $this->env);
		}
		elseif ($subject instanceof ResolvedCollection && $body instanceof CollectionValueModification)
		{
			throw new NotImplementedException;
		}
		else
		{
			throw new \LogicException('Unsupported resource and deserialized body type for a Modify request');
		}

		// Return the same resource.
		return new ResourceRequestResult($subject);
	}
}
