<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Exception\ConfigurationException;
use Light\ObjectService\Exception\SerializationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Service\RequestComponents;
use Szyman\ObjectService\Service\RequestResult;
use Szyman\ObjectService\Service\ResponseCreator;

/**
 * A response creator that can handle request results of type {@link CustomRequestResult}.
 */
final class CustomResponseCreator implements ResponseCreator
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
	 * @throws \InvalidArgumentException    Thrown if <kbd>$requestResult</kbd> is not of a supported type.
	 * @throws SerializationException        Thrown if a problem was encountered while creating the response body.
	 * @throws ConfigurationException        Thrown if a problem with the configuration prevents the creation of a response.
	 */
	public function newResponse(Request $request, RequestResult $requestResult, RequestComponents $requestComponents = null)
	{
		if ($requestResult instanceof CustomRequestResult)
		{
			return $requestResult->getResponse($this->structureSerializer, $this->dataSerializer, $this->responseContentTypeMap);
		}
		else
		{
			return null;
		}
	}
}