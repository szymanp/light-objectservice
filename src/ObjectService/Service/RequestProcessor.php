<?php
namespace Light\ObjectService\Service;

use Light\Exception\InvalidReturnValue;
use Light\ObjectAccess\Exception\AddressResolutionException;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectService\Exception\NotFound;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Operation\ExecutionParameters;
use Light\ObjectService\Resource\Projection\Projector;

class RequestProcessor
{
	/** @var ExecutionParameters */
	protected $executionParameters;
	/** @var Request */
	protected $request;

	/** @var mixed */
	private $entity;
	/** @var \Exception */
	private $exception;

	public function __construct(ExecutionParameters $executionParams, Request $request)
	{
		$this->executionParameters = $executionParams;
		$this->request = $request;
	}

	public function process()
	{
		try
		{
			$this->processWithoutErrorHandling();
		}
		catch (\Exception $e)
		{
			$this->exception = $e;
		}
	}

	/**
	 * Returns true if the result is an exception.
	 * @return bool
	 */
	public function hasException()
	{
		return !is_null($this->exception);
	}

	/**
	 * Returns true if the result is a data entity.
	 * @return bool
	 */
	public function hasEntity()
	{
		return !is_null($this->entity);
	}

	/**
	 * Returns the exception that is the result of processing the request.
	 * @return \Exception
	 */
	public function getException()
	{
		return $this->exception;
	}

	/**
	 * Returns the entity that is the result of processing the request.
	 * @return mixed
	 */
	public function getEntity()
	{
		return $this->entity;
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

		if (!is_null($projectedResource))
		{
			$projector = new Projector();
			$this->entity = $projector->project($projectedResource, $this->request->getSelection());
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
			throw new NotFound($address->getAsString(), "Could not find any resource matching the URL");
		}

		$relativeAddressReader = new RelativeAddressReader($relativeAddress);
		// TODO Supply the selection from the request to the reader.
		return $relativeAddressReader->read();
	}

}