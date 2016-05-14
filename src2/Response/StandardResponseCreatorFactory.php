<?php
namespace Szyman\ObjectService\Response;

use Symfony\Component\HttpFoundation\Request;
use Szyman\Exception\UnexpectedValueException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Response\StandardResponseCreatorFactory\ResponseCreatorEntry;
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

	/** @var ResponseCreatorEntry[]  */
	private $responseCreators = array();

	/**
	 * Constructs a new ResponseCreatorFactory.
	 * @param ResponseContentTypeMap $contentTypeMap
	 * @param ResponseCreatorEntry[] $responseCreators
	 */
	public function __construct(ResponseContentTypeMap $contentTypeMap, array $responseCreators = array())
	{
		$this->contentTypeMap = $contentTypeMap;
		$this->responseCreators = [
			new ResponseCreatorEntry(ResourceRequestResult::class,  function($stru, $data, $ctMap) { return new StandardResourceResponseCreator($stru, $data, $ctMap); }),
			new ResponseCreatorEntry(ExceptionRequestResult::class, function($stru, $data, $ctMap) { return new StandardErrorResponseCreator($stru, $data, $ctMap); })
		];
		$this->responseCreators = array_merge($responseCreators, $this->responseCreators);
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

		foreach($this->responseCreators as $entry)
		{
			if ($entry->isValidFor($requestResult))
			{
				return $entry->create($serializers->structure, $serializers->data, $this->contentTypeMap);
			}
		}

		return null;
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

// Namespace for inner classes
namespace Szyman\ObjectService\Response\StandardResponseCreatorFactory;

use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Response\DataSerializer;
use Szyman\ObjectService\Response\StructureSerializer;
use Szyman\ObjectService\Service\RequestResult;

/**
 * Maps a RequestResult subclass to a ResponseCreator.
 */
final class ResponseCreatorEntry
{
	private $className, $fn;

	/**
	 * @param          $className	A class name for a {@link RequestResult} subclass.
	 * @param callable $fn 			A constructor function returning a ResponseCreator.
	 *                     			It takes three arguments: a StructureSerializer, a DataSerializer and a ResponseContentTypeMap.
	 */
	public function __construct($className, \Closure $fn)
	{
		$this->className = $className;
		$this->fn = $fn;
	}

	public function isValidFor(RequestResult $requestResult)
	{
		$className = $this->className;
		return $requestResult instanceof $className;
	}

	public function create(StructureSerializer $structureSerializer, DataSerializer $dataSerializer, ResponseContentTypeMap $responseContentTypeMap)
	{
		$fn = $this->fn;
		return $fn($structureSerializer, $dataSerializer, $responseContentTypeMap);
	}
}