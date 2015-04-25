<?php
namespace Light\ObjectService\Formats\Json\Deserializers;

use Light\ObjectService\Exception\MalformedRequest;
use Light\ObjectService\Json\Request\Operation\AppendOperationReader;
use Light\ObjectService\Json\Request\Operation\UpdateOperationReader;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Service\Protocol\DeserializedResult;
use Light\ObjectService\Service\Protocol\Deserializer;
use Symfony\Component\HttpFoundation\Request;

class SimpleTreeDeserializer implements Deserializer
{
	/**
	 * Returns a list of content types supported by this serializer.
	 * @return string[]
	 */
	public function getContentTypes()
	{
		return array('application/json', 'text/json');
	}

	/**
	 * Deserializes the request entity.
	 * @param Request             $httpRequest
	 * @param ExecutionParameters $executionParameters
	 * @return DeserializedResult
	 * @throws MalformedRequest
	 */
	public function deserialize(Request $httpRequest, ExecutionParameters $executionParameters)
	{
		$result = new DeserializedResult();

		$method = $httpRequest->getMethod();
		$json = json_decode($httpRequest->getContent());

		if (is_null($json))
		{
			$json = new \stdClass;
		}

		switch($method)
		{
			case "POST":
				$reader = new AppendOperationReader($executionParameters);
				break;
			case "PATCH":
				$reader = new UpdateOperationReader($executionParameters);
				break;
				// TODO
				// Is the Deserializer concept correct in terms of the input and output?
				// As it is now the deserializer needs to implement the full logic of the protocol.
				// It needs to know what kind of operation should the POST method result in.
				// But this depends on the resource (?? does it really ??)
			default:
				throw new MalformedRequest("Unexpected method " . $method);
		}

		$result->setOperations(array($reader->read($json)));

		return $result;
	}
}