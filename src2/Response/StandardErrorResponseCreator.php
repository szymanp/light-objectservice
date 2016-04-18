<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\Util\EmptyResourceAddress;
use Light\ObjectAccess\Type\ComplexTypeHelper;
use Light\ObjectAccess\Type\TypeProvider;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultComplexType;
use Light\ObjectAccess\Type\Util\DefaultProperty;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Exception\ConfigurationException;
use Light\ObjectService\Exception\HttpExceptionInformation;
use Light\ObjectService\Exception\SerializationException;
use Light\ObjectService\Resource\Projection\Projector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\Exception\InvalidArgumentTypeException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Service\ExceptionRequestResult;
use Szyman\ObjectService\Service\RequestComponents;
use Szyman\ObjectService\Service\RequestResult;
use Szyman\ObjectService\Service\ResponseCreator;

class StandardErrorResponseCreator implements ResponseCreator
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
	 * @param Request           $request
	 * @param RequestResult     $requestResult
	 * @param RequestComponents $requestComponents
	 * @return Response
	 * @throws \InvalidArgumentException	Thrown if <kbd>$requestResult</kbd> is not of a supported type.
	 * @throws SerializationException		Thrown if a problem was encountered while creating the response body.
	 * @throws ConfigurationException		Thrown if a problem with the configuration prevents the creation of a response.
	 */
	final public function newResponse(Request $request, RequestResult $requestResult, RequestComponents $requestComponents = null)
	{
		// Check if the request result is applicable.
		if (!($requestResult instanceof ExceptionRequestResult))
		{
			throw new InvalidArgumentTypeException('$requestResult', $requestResult, ExceptionRequestResult::class);
		}

		// Build the exception resource.
		$typeRegistry = new TypeRegistry($this->getTypeProvider());
		$typeHelper = $typeRegistry->getTypeHelperByValue($requestResult->getException());
		if (!($typeHelper instanceof ComplexTypeHelper))
		{
			throw new ConfigurationException('The configured type provider did not return a ComplexTypeHelper (but %1) for the requested exception', $typeHelper);
		}
		$resource = new ResolvedObject($typeHelper, $requestResult->getException(), EmptyResourceAddress::create(), Origin::unavailable());

		// Project and serialize the resource.
		$projector = new Projector();
		$projected = $projector->project($resource);
		$intermediate = $this->structureSerializer->serializeStructure($projected);
		$content = $this->dataSerializer->serializeData($intermediate);

		// Prepare the headers.
		$headers = array();
		$contentType = $this->responseContentTypeMap->getContentType($resource, $this->dataSerializer);
		if (is_null($contentType))
		{
			throw new ConfigurationException('The configured content type map did not return a match');
		}
		$headers['CONTENT_TYPE'] = $contentType;

		// Prepare the response object.
		$status = $this->getStatusCode($requestResult->getException());
		return $this->createResponse($content, $status, $headers);
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
	 * Returns the status code for this exception object.
	 * @param \Exception $e
	 * @return int
	 */
	protected function getStatusCode(\Exception $e)
	{
		if ($e instanceof HttpExceptionInformation)
		{
			return $e->getHttpStatusCode();
		}
		else
		{
			return Response::HTTP_INTERNAL_SERVER_ERROR;
		}
	}

	/**
	 * Return a type provider for handling <kbd>Exception</kbd> classes.
	 * @return TypeProvider
	 */
	protected function getTypeProvider()
	{
		$provider = new DefaultTypeProvider();

		$exceptionType = new DefaultComplexType(\Exception::class);
		$exceptionType->addProperty(new DefaultProperty('message', 'string'));
		$provider->addType($exceptionType);

		return $provider;
	}
}