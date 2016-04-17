<?php
namespace Szyman\ObjectService\Response;

use Symfony\Component\HttpFoundation\Request;
use Szyman\Exception\UnexpectedValueException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Service\ExceptionRequestResult;
use Szyman\ObjectService\Service\RequestComponents;
use Szyman\ObjectService\Service\RequestResult;
use Szyman\ObjectService\Service\ResourceRequestResult;
use Szyman\ObjectService\Service\ResponseCreator;
use Szyman\ObjectService\Service\ResponseCreatorFactory;

class StandardResponseCreatorFactory implements ResponseCreatorFactory
{
	/** @var ResponseContentTypeMap */
	private $contentTypeMap;

	public function __construct(ResponseContentTypeMap $contentTypeMap)
	{
		$this->contentTypeMap = $contentTypeMap;
	}

	/**
	 * @return ResponseContentTypeMap
	 */
	final protected function getResponseContentTypeMap()
	{
		return $this->contentTypeMap;
	}

	/** @inheritdoc */
	final public function newResponseCreator(Request $request, RequestResult $requestResult, RequestComponents $requestComponents = null)
	{
		$serializers = $this->findSerializersFromAccepts($request);
		if (is_null($serializers))
		{
			return null;
		}

		if ($requestResult instanceof ResourceRequestResult)
		{
			return new StandardResourceResponseCreator($serializers->structure, $serializers->data, $this->contentTypeMap);
		}
		elseif ($requestResult instanceof ExceptionRequestResult)
		{
			return new StandardErrorResponseCreator($serializers->structure, $serializers->data, $this->contentTypeMap);
		}
		else
		{
			return null;
		}
	}

	private function findSerializersFromAccepts(Request $request)
	{
		foreach($request->getAcceptableContentTypes() as $accept)
		{
			$data = $this->getDataSerializer($accept);
			if (is_null($data))
			{
				continue;
			}
			elseif (!($data instanceof DataSerializer))
			{
				throw UnexpectedValueException::newInvalidReturnValue($this, 'getDataSerializer', $data, 'Expecting DataSerializer');
			}

			$structure = $this->getStructureSerializer($accept);
			if (is_null($structure))
			{
				continue;
			}
			elseif (!($structure instanceof StructureSerializer))
			{
				throw UnexpectedValueException::newInvalidReturnValue($this, 'getStructureSerializer', $structure, 'Expecting StructureSerializer');
			}

			$result = new \stdClass();
			$result->structure = $structure;
			$result->data	   = $data;
			return $result;
		}
		return null;
	}

	/**
	 * Returns a <kbd>StructureSerializer</kbd> matching the requested content-type.
	 *
	 * This method is called for each content-type specified in the HTTP Accepts header.
	 * It should return a {@link StructureSerializer} matching the content-type, or NULL otherwise.
	 *
	 * @param string	$contentType
	 * @return StructureSerializer|null	A StructureSerializer, if the content-type matches; otherwise, NULL.
	 */
	protected function getStructureSerializer($contentType)
	{
		// This is the only serializer we currently support.
		return new HalSerializer();
	}

	/**
	 * Returns a <kbd>DataSerializer</kbd> matching the requested content-type.
	 *
	 * This method is called for each content-type specified in the HTTP Accepts header.
	 * It should return a {@link DataSerializer} matching the content-type, or NULL otherwise.
	 *
	 * @param string	$contentType
	 * @return DataSerializer|null
	 */
	protected function getDataSerializer($contentType)
	{
		if ($contentType == 'application/json')
		{
			return new JsonDataSerializer();
		}
	}
}