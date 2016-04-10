<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Exception\AddressResolutionException;
use Light\ObjectAccess\Query\Query;
use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectAccess\Resource\ResolvedCollection;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\Util\DefaultRelativeAddress;
use Light\ObjectAccess\Type\Type;
use Light\ObjectService\Exception\MethodNotAllowed;
use Light\ObjectService\Exception\NotFound;
use Light\ObjectService\Exception\UnsupportedMediaType;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Symfony\Component\HttpFoundation\Request;
use Szyman\ObjectService\Configuration\Configuration;

/**
 * Reads a HTTP Request according to REST rules.
 *
 * Below is the general explanation of the handling of requests.
 * Not all of the functionality described depends on this class alone.
 *
 * GET: 
 * All types of resources (Simple, Complex, Collections) can be read.
 * The resource to be read is identified by the URL. The resource MUST exist.
 * There is no request body, so the body content-type does not matter.
 *
 * PUT:
 * All types or resources can be PUT:
 * - for Simple values, PUT sets a new value
 * - for Complex values, PUT sets all specified fields while those not specified are set to default/null values
 * - for Collections, PUT removes all elements from the collection and adds only the specified ones
 *
 * This method generally creates or replaces the resource specified at URL. The resource MAY NOT exist.
 * The body content-type MUST be in a "update" format.
 * The underlying resource may be an object or a collection.
 *
 * PATCH:
 * Only Complex and Collection resources can be patched.
 * - Patching Simple values would require some specialized protocol on how to modify them.
 * - For Complex values, PATCH sets only the fields specified in the request
 * - For Collections, PATCH specifies which elements to remove and which to add
 *
 * The resource to be patched is identified by the request-uri. The resource MUST exist.
 * The body content-type MUST be in a "update" format.
 * The underlying resource may be an object or a collection.
 *
 * DELETE:
 * Only Complex and Collection resources can be deleted.
 * - For Complex values, DELETE removes the object from its underlying collection.
 *   Depending on the underlying implementation, this might mean that the object is physically deleted.
 *   The API does not specify the behavior.
 * - For Collections, DELETE removes the elements from the collection.
 *   The collection itself cannot be deleted.
 *   The body might contain the scope of elements to be deleted.
 *
 * This method deletes the resource at the specified URL. The resource MUST exist.
 * The body is optional. It can contain extra arguments for the deletion,
 * such as the scope of elements to be deleted from a collection.
 *
 * POST:
 * Only Complex and Collection resources can be used with POST.
 * - For Complex values, POST is used to perform an action on the resource.
 * - For Collections, POST can either:
 *   - Append a new element to the collection and return the URL of the added element.
 *     The body of the request is the specification of the new element.
 *   - Perform an action on the collection.
 *     The body of the request is the specification of the action.
 *
 * Post can execute one of the following actions:
 * - If content-type of the body is in "update" format, then it adds a resource to a collection.
 *   The resource identified by the URL MUST exist and MUST be a collection.
 * - If content-type of the body is in an "action" format, then the specified action is executed.
 *	 The underlying resource MUST exist and may be either an object or a collection.
 */
class RestRequestReader
{
	/** @var Configuration */
	private $conf;

	public function __construct(Configuration $conf)
	{
		$this->conf = $conf;
	}

	public function readRequest(Request $request)
	{
		$result = RequestComponents::newBuilder();

		// Determine the endpoint-relative address of the resource at request-uri.
		$address = $this->getResourceAddress($request);
		$result->endpointAddress($address);

		// Determine the subject resource and the resource at request-uri.
		$resources = $this->determineRequestResources($request, $address);
		$result->subjectResource($resources->subjectResource);
		if (!is_null($resources->requestResource))
		{
			$result->requestUriResource($resources->requestResource);
		}
		if (!is_null($resources->relativeAddress))
		{
			$result->relativeAddress($resources->relativeAddress);
		}

		// Determine the request body type.
		// Note that the result from RequestBodyTypeMap could be NULL.
		// In this case we will have to determine the type later based on other factors.
		$contentType = $request->headers->get('CONTENT_TYPE');
		$requestBodyType = empty($contentType) ?
			RequestBodyType::get(RequestBodyType::NONE) :
			$this->conf->getRequestBodyTypeMap()->getRequestBodyType($contentType);

		if (is_null($requestBodyType))
		{
			$requestBodyType = $this->getDefaultRequestBodyType($request->getMethod());
		}

		// Determine the request type
		$requestType = $this->determineRequestType($request->getMethod(), $requestBodyType, $resources->requestResource);
		$result->requestType($requestType);

		// Instantiate the deserializer
		if (!$requestBodyType->is(RequestBodyType::NONE))
		{
			$resourceType = $this->determineBodyResourceType(
				$requestType,
				$requestBodyType,
				$resources->subjectResource,
				$resources->requestResource);
		
			$deserializer = $this->conf->getRequestBodyDeserializerFactory()->newRequestBodyDeserializer(
				$deserializerType = RequestBodyDeserializerType::fromBodyAndResourceType($requestBodyType, $resourceType),
				$contentType,
				$resources->subjectResource->getType()
			);
			if (is_null($deserializer))
			{
				throw new UnsupportedMediaType($contentType, "No deserializer of type " . $deserializerType->getName() . " found");
			}
			$result->deserializer($deserializer);
		}

		return $result->build();
	}

	/**
	 * Returns the URI without the query section.
	 * @param Request $request
	 * @return string
	 */
	private function getUriWithoutQuery(Request $request)
	{
		$uri = $request->getUri();

		// Remove the query section
		if (($qm = strpos($uri, '?')) > -1)
		{
			return substr($uri, 0, $qm);
		}
		else
		{
			return $uri;
		}
	}

	/**
	 * @param Request $request
	 * @return \Light\ObjectService\Resource\Addressing\EndpointRelativeAddress
	 * @throws NotFound
	 */
	private function getResourceAddress(Request $request)
	{
		$address = $this->conf->getEndpointRegistry()->getResourceAddress($this->getUriWithoutQuery($request));

		if (is_null($address))
		{
			throw new NotFound($request->getUri(), "No endpoint matching this address was found");
		}

		if ($request->query->has("count") || $request->query->has("offset"))
		{
			$count  = $request->query->getInt("count", null);
			$offset = $request->query->getInt("offset", null);
			$limitScope = Scope::createWithQuery(Query::emptyQuery(), $count, $offset);
			$address = $address->appendScope($limitScope);
		}

		return $address;
	}

	/**
	 * Creates a new RelativeAddressReader with the given address.
	 * @param EndpointRelativeAddress $address
	 * @return RelativeAddressReader
	 * @throws NotFound
	 */
	private function newRelativeAddressReader(EndpointRelativeAddress $address)
	{
		$relativeAddress = $address->getEndpoint()->findResource($address->getPathElements());

		if (is_null($relativeAddress))
		{
			throw new NotFound($address->getAsString(), "No matching resource found");
		}

		return new RelativeAddressReader($relativeAddress);
	}

	/**
	 * Finds the subject resource and request-uri resource of this request.
	 *
	 * @param Request                 $request
	 * @param EndpointRelativeAddress $address
	 * @return \stdClass
	 * @throws MethodNotAllowed
	 * @throws NotFound
	 */
	private function determineRequestResources(Request $request, EndpointRelativeAddress $address)
	{
		$relativeAddressReader = $this->newRelativeAddressReader($address);

		$result = new \stdClass;

		try
		{
			// Find the resource specified in the URL.
			$resource = $relativeAddressReader->read();

			switch($request->getMethod())
			{
				// For these methods, the subject resource is always identified by the request-uri.
				case 'GET':
				case 'PATCH':
				case 'DELETE':
				case 'POST':
					if (is_null($resource))
					{
						// One of the resources in the URL path chain resolved to a NULL.
						throw new NotFound($request->getUri(), 'Target resource not found');
					}

					$result->subjectResource = $resource;
					$result->requestResource = $resource;
					$result->relativeAddress = null;
					return $result;

				// For the PUT method, the subject resource is a collection (if we replace or create a collection element),
				// or a complex object (if we replace a simple value of a field).
				case 'PUT':
					if (is_null($resource))
					{
						// There resource at uri does not exist, therefore this is the case when we create a new one.
						// We can only create a collection element.
						$trace = $relativeAddressReader->getLastResolutionTrace();
						if (count($trace) == 0)
						{
							throw new MethodNotAllowed("Requested resource has no parent collection");
						}

						$result->requestResource = null;
						$result->subjectResource = $trace->last()->getResource();
						$result->relativeAddress = new DefaultRelativeAddress($result->subjectResource);
						$elements = $address->getPathElements();
						$result->relativeAddress->appendElement(end($elements));

						if ($result->subjectResource instanceof ResolvedCollection)
						{
							return $result;
						}
						else
						{
							throw new MethodNotAllowed("Parent of requested resource is not a collection");
						}
					}
					elseif ($resource instanceof ResolvedCollection)
					{
						// The resource at uri is a collection.
						// This is the case where we replace an entire collection.
						$result->subjectResource = $resource;
						$result->requestResource = $resource;
						$result->relativeAddress = null;
						return $result;
					}
					else // Resource at URL is a value
					{
						$trace = $relativeAddressReader->getLastResolutionTrace();
						if (count($trace) < 2)
						{
							throw new MethodNotAllowed("Requested resource has no parent collection or object");
						}

						$result->requestResource = $resource;
						$result->subjectResource = $trace->last()->previous()->getResource();
						$result->relativeAddress = new DefaultRelativeAddress($result->subjectResource);
						$result->relativeAddress->appendElement($trace->last()->getPathElement());

						if ($result->subjectResource instanceof ResolvedCollection
							|| $result->subjectResource instanceof ResolvedObject)
						{
							return $result;
						}
						else
						{
							throw new MethodNotAllowed("Parent of requested resource is not a collection nor an object");
						}
					}
				default:
					throw new \LogicException($request->getMethod());
			}
		}
		catch (AddressResolutionException $e)
		{
			// This exception indicates that the path in the URL didn't match the type structure of the resources.
			// For example, an attempt to access an element of a resource that was not a collection was made.
			throw new NotFound(
				$request->getUri(),
				"Static type resolution failed",
				0,
				$e);
		}
	}

	/**
	 * Returns the default request body type corresponding to the HTTP method.
	 * @param string $method	A HTTP method.
	 * @return RequestBodyType
	 * @throws MethodNotAllowed
	 */
	private function getDefaultRequestBodyType($method)
	{
		switch($method)
		{
			case 'GET':
				return RequestBodyType::get(RequestBodyType::NONE);

			case 'PUT':
				return RequestBodyType::get(RequestBodyType::REPRESENTATION);

			case 'PATCH':
				return RequestBodyType::get(RequestBodyType::MODIFICATION);

			case 'DELETE':
				return RequestBodyType::get(RequestBodyType::SELECTION);

			case 'POST':
				return RequestBodyType::get(RequestBodyType::REPRESENTATION);

			default:
				throw new MethodNotAllowed("Cannot determine default body type for method " . $method);

		}
	}

	/**
	 * Determines the type of the requested action.
	 *
	 * @param string           $method
	 * @param RequestBodyType  $requestBodyType
	 * @param ResolvedResource $requestUriResource
	 * @return RequestType
	 * @throws MethodNotAllowed
	 */
	private function determineRequestType($method, RequestBodyType $requestBodyType, ResolvedResource $requestUriResource = null)
	{
		switch($method)
		{
			case 'GET':
				assert($requestBodyType->is(RequestBodyType::NONE));
				return RequestType::get(RequestType::READ);

			case 'PUT':
				assert($requestBodyType->is(RequestBodyType::REPRESENTATION));
				if (is_null($requestUriResource))
				{
					return RequestType::get(RequestType::CREATE);
				}
				else
				{
					return RequestType::get(RequestType::REPLACE);
				}

			case 'PATCH':
				assert($requestBodyType->is(RequestBodyType::MODIFICATION));
				return RequestType::get(RequestType::MODIFY);

			case 'DELETE':
				return RequestType::get(RequestType::DELETE);

			case 'POST':
				if ($requestBodyType->is(RequestBodyType::ACTION))
				{
					return RequestType::get(RequestType::ACTION);
				}
				else
				{
					assert($requestBodyType->is(RequestBodyType::REPRESENTATION));
					return RequestType::get(RequestType::CREATE);
				}

			default:
				throw new MethodNotAllowed();
		}
	}

	/**
	 * Determines the type of the resource of the body.
	 *
	 * @param RequestTye       $requestType
	 * @param RequestBodyType  $requestBodyType
	 * @param ResolvedResource $subjectResource
	 * @param ResolvedResource $requestResource
	 * @return Type
	 */
	private function determineBodyResourceType(RequestType $requestType, RequestBodyType $requestBodyType, ResolvedResource $subjectResource, ResolvedResource $requestResource = null)
	{
		switch($requestBodyType->getValue())
		{
			case RequestBodyType::NONE:
				throw new \LogicException();
			
			case RequestBodyType::REPRESENTATION:
				if ($requestType->is(RequestType::CREATE))
				{
					// Example requests:
					// PUT /users/1020 (where user 1020 does not exist)
					// POST /users     (creates a new user in the collection)
					if ($subjectResource instanceof ResolvedCollection)
					{
						return $subjectResource->getTypeHelper()->getBaseTypeHelper()->getType();
					}
					else
					{
						throw new \LogicException('The subject resource for creation of a new element must be a collection');
					}
				}
				elseif ($requestType->is(RequestType::REPLACE))
				{
					// Example requests:
					// PUT /users/1020      (where user 1020 does exist)
					// PUT /users           (replaces the collection)
					// PUT /users/1020/name (replaces the field value)
					return $requestResource->getType();
				}
				throw new \LogicException();
			
			case RequestBodyType::MODIFICATION:
			case RequestBodyType::ACTION:
			case RequestBodyType::SELECTION:
				// TODO: Is this correct?
				return $subjectResource->getType();

			default:
				throw new \LogicException();
		}
	}
}