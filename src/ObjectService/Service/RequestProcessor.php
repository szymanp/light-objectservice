<?php
namespace Light\ObjectService\Service;

use Light\Exception\InvalidReturnValue;
use Light\ObjectAccess\Exception\AddressResolutionException;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Resource\Projection\Projector;

class RequestProcessor
{
	/** @var ExecutionParameters */
	protected $executionParameters;
	/** @var Request */
	protected $request;
	/** @var Response */
	protected $response;

	public function __construct(ExecutionParameters $executionParams, Request $request, Response $response)
	{
		$this->executionParameters = $executionParams;
		$this->request = $request;
		$this->response = $response;
	}

	public function process()
	{
		try
		{
			$this->processWithoutErrorHandling();
		}
		catch (\Exception $e)
		{
			$this->response->setException($e);
		}

		$this->response->send();
	}

	protected function processWithoutErrorHandling()
	{
		$resource = $this->getResource();
		$projectedResource = $resource;

		$operations = $this->request->getOperations();
		if (!is_array($operations))
		{
			$operations = array();
		}

		foreach($operations as $operation)
		{
			$operation->execute($resource, $this->executionParameters);
		}

		$this->response->setOperations($operations);

		if (!is_null($projectedResource))
		{
			$projector = new Projector();
			$dataEntity = $projector->project($projectedResource, $this->request->getSelection());
			$this->response->setEntity($dataEntity);
		}
	}

	/**
	 * @return \Light\ObjectAccess\Resource\ResolvedResource
	 * @throws AddressResolutionException
	 */
	final protected function getResource()
	{
		$address = $this->request->getResourceAddress();

		if (!($address instanceof EndpointRelativeAddress))
		{
			throw new InvalidReturnValue($this->request, "getResourceAddress", $address, EndpointRelativeAddress::class);
		}

		$relativeAddress = $address->getEndpoint()->findResource($address->getPathElements());

		if (is_null($relativeAddress))
		{
			throw new AddressResolutionException("Address <%1> could not be resolved to any resource", $address->getAsString());
		}

		$relativeAddressReader = new RelativeAddressReader($relativeAddress);
		// TODO Supply the selection from the request to the reader.
		return $relativeAddressReader->read();
	}

}