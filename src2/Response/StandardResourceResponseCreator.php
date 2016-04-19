<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedValue;
use Light\ObjectAccess\Resource\Addressing\CanonicalResourceAddress;
use Light\ObjectAccess\Type\Complex\CanonicalAddress;
use Light\ObjectAccess\Type\ComplexType;
use Light\ObjectAccess\Type\SimpleType;
use Light\ObjectService\Exception\SerializationException;
use Light\ObjectService\Exception\ConfigurationException;
use Light\ObjectService\Resource\Projection\Projector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\Exception\InvalidArgumentTypeException;
use Szyman\Exception\InvalidArgumentValueException;
use Szyman\Exception\UnexpectedValueException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Response\StandardResourceResponseCreator\ResponseContent;
use Szyman\ObjectService\Service\RequestComponents;
use Szyman\ObjectService\Service\RequestResult;
use Szyman\ObjectService\Service\RequestType;
use Szyman\ObjectService\Service\ResourceRequestResult;
use Szyman\ObjectService\Service\ResponseCreator;

class StandardResourceResponseCreator implements ResponseCreator
{
	/** @var StructureSerializer */
	private $structureSerializer;
	/** @var DataSerializer */
	private $dataSerializer;
	/** @var ResponseContentTypeMap */
	private $responseContentTypeMap;

	public function __construct(StructureSerializer $structureSerializer, DataSerializer $dataSerializer, ResponseContentTypeMap $responseContentTypeMap)
	{
		$this->structureSerializer = $structureSerializer;
		$this->dataSerializer = $dataSerializer;
		$this->responseContentTypeMap = $responseContentTypeMap;
	}

	/**
	 * Creates a new Response object.
	 * @param Request           $request            The HTTP request for which a response is being made.
	 * @param RequestResult     $requestResult      The content of the response.
	 * @param RequestComponents $requestComponents  Additional information about the request. In some cases
	 *                                              this information might not be available.
	 * @return Response
	 * @throws \InvalidArgumentException     Thrown if <kbd>$requestResult</kbd> is not of a supported type.
	 * @throws SerializationException        Thrown if a problem was encountered while creating the response body.
	 * @throws ConfigurationException        Thrown if a problem with the configuration prevents the creation of a response.
	 */
	final public function newResponse(Request $request, RequestResult $requestResult, RequestComponents $requestComponents = null)
	{
		// Check if the request result is applicable.
		if (!($requestResult instanceof ResourceRequestResult))
		{
			throw new InvalidArgumentTypeException('$requestResult', $requestResult, ResourceRequestResult::class);
		}

		// RequestComponents must not be NULL for this ResponseCreator.
		if (is_null($requestComponents))
		{
			throw new InvalidArgumentValueException('$requestComponents', $requestComponents, 'Value cannot be NULL');
		}

		// Setup some default headers
		$headers = array();

		if (!is_null($requestComponents))
		{
			// Append a Location header
			if ($request->getMethod() == Request::METHOD_POST && $requestComponents->getRequestType()->is(RequestType::CREATE))
			{
				$this->appendLocationHeader($requestResult->getResource(), $headers);
			}
		}

		// Invoke the helper method to determine what we want to send in the response.
		$responseContent = $this->getResponseContent($request, $requestResult, $requestComponents);

		if (!($responseContent instanceof ResponseContent))
		{
			throw UnexpectedValueException::newInvalidReturnValue($this, 'getResponseContent', $responseContent, 'Expecting ResponseContent');
		}

		if (is_null($responseContent->getResource()))
		{
			$response = $this->createResponse('', $responseContent->getStatusCode(), $headers);
		}
		else
		{
			// Project and serialize the resource.
			$projector = new Projector();
			$projected = $projector->project($responseContent->getResource(), $responseContent->getSelection());
			$intermediate = $this->structureSerializer->serializeStructure($projected);
			$content = $this->dataSerializer->serializeData($intermediate);

			// Append the content-type header.
			$contentType = $this->responseContentTypeMap->getContentType($responseContent->getResource(), $this->dataSerializer);
			if (is_null($contentType))
			{
				throw new ConfigurationException('The configured content type map did not return a match');
			}
			$headers['CONTENT_TYPE'] = $contentType;

			// Build the response.
			$response = $this->createResponse($content, $responseContent->getStatusCode(), $headers);
		}

		return $response;
	}

	private function appendLocationHeader(ResolvedResource $resource, array & $headers)
	{
		$type = $resource->getType();
		if ($resource->getAddress() instanceof CanonicalResourceAddress)
		{
			$address = $resource->getAddress();
		}
		elseif ($type instanceof CanonicalAddress && $resource instanceof ResolvedValue)
		{
			$address = $type->getCanonicalAddress($resource->getValue());
		}
		else
		{
			$address = $resource->getAddress();
		}

		if ($address->hasStringForm())
		{
			$headers['LOCATION'] = $address->getAsString();
		}
	}

	/**
	 * Return a new Response instance.
	 * @param mixed $content
	 * @param int   $status
	 * @param array $headers
	 * @return Response
	 */
	protected function createResponse($content, $status, array $headers)
	{
		return new Response($content, $status, $headers);
	}

	/**
	 * Returns the status code and content of the response.
	 * @param Request           	$request
	 * @param ResourceRequestResult $requestResult
	 * @param RequestComponents 	$requestComponents
	 * @return ResponseContent
	 */
	protected function getResponseContent(Request $request, ResourceRequestResult $requestResult, RequestComponents $requestComponents)
	{
		$requestType = $requestComponents->getRequestType();

		if ($requestType->is(RequestType::DELETE) && $requestComponents->getSubjectResource()->getType() instanceof ComplexType)
		{
			// Delete a single object. E.g. DELETE /users/1020
			return new ResponseContent(Response::HTTP_NO_CONTENT);
		}
		elseif ($requestType->is(RequestType::REPLACE) && $requestComponents->getRequestUriResource()->getType() instanceof SimpleType)
		{
			// Update a simple field. E.g. PUT /users/1020/name
			return new ResponseContent(Response::HTTP_NO_CONTENT);
		}
		elseif ($requestType->is(RequestType::CREATE) && $request->getMethod() == Request::METHOD_PUT)
		{
			// TODO Selection?
			return new ResponseContent(Response::HTTP_CREATED, $requestResult->getResource());
		}
		else
		{
			return new ResponseContent(Response::HTTP_OK, $requestResult->getResource());
		}
	}

}

// Namespace for inner classes
namespace Szyman\ObjectService\Response\StandardResourceResponseCreator;

use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectService\Resource\Selection\Selection;

final class ResponseContent
{
	/** @var int */
	private $statusCode;
	/** @var ResolvedResource|null */
	private $resource;
	/** @var Selection */
	private $selection;

	function __construct($statusCode, ResolvedResource $resource = null, Selection $selection = null)
	{
		$this->statusCode = $statusCode;
		$this->resource = $resource;
		$this->selection = $selection;
	}

	/**
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * @return ResolvedResource
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @return Selection|null
	 */
	public function getSelection()
	{
		return $this->selection;
	}
}