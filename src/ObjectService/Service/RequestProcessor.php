<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Exception\AddressResolutionException;
use Light\ObjectAccess\Resource\RelativeAddressReader;
use Light\ObjectService\Exception\NotFound;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Szyman\ObjectService\Service\ExecutionParameters;
use Light\ObjectService\Resource\Projection\Projector;
use Light\ObjectService\Resource\Selection\RootSelectionProxy;
use Szyman\Exception\UnexpectedValueException;

class RequestProcessor
{
	/** @var \Szyman\ObjectService\Service\ExecutionParameters */
	protected $executionParameters;
	/** @var Request */
	protected $request;

	/** @var mixed */
	private $entity;
	/** @var \Exception */
	private $exception;

	/** @var bool */
	private $errorHandling = true;

	public function __construct(ExecutionParameters $executionParams, Request $request)
	{
		$this->executionParameters = $executionParams;
		$this->request = $request;
	}

	public function process()
	{
		if ($this->errorHandling)
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
		else
		{
			$this->processWithoutErrorHandling();
		}
	}

	/**
	 * Disables error handling in process() call.
	 */
	public function disableErrorHandling()
	{
		$this->errorHandling = false;
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
		if (is_null($resource))
		{
			throw new NotFound($this->request->getResourceAddress()->getAsString(), "RelativeAddressReader returned no resource");
		}

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

		// Transfer the changes done during operation processing.
		// We need to do it before projecting the result resource as otherwise the changes might not be visible.
		$this->executionParameters->getTransaction()->transfer();

		if (!is_null($projectedResource))
		{
			$projector = new Projector();

			$selection = $this->request->getSelection();
			if ($selection instanceof RootSelectionProxy)
			{
				$selection->prepare($projectedResource->getTypeHelper());
			}
			$this->entity = $projector->project($projectedResource, $selection);
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
			throw UnexpectedValueException::newInvalidReturnValue($this->request, "getResourceAddress", $address, "Expecting " . EndpointRelativeAddress::class);
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