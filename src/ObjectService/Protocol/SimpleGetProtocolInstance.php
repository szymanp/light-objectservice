<?php
namespace Light\ObjectService\Protocol;

use Light\Exception\Exception;
use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Exception\HttpExceptionInformation;
use Light\ObjectService\Exception\NotFound;
use Light\ObjectService\Resource\Projection\DataEntity;
use Light\ObjectService\Service\Protocol\ProtocolInstance;
use Light\ObjectService\Service\Protocol\SerializationHelper;
use Light\ObjectService\Service\Request;
use Light\ObjectService\Service\Util\SettableRequest;
use Symfony\Component\HttpFoundation;

class SimpleGetProtocolInstance extends AbstractProtocolInstance
{
	/** @var SimpleGetProtocol */
	private $protocol;
	/** @var Transaction */
	private $transaction;

	public function __construct(SimpleGetProtocol $protocol, HttpFoundation\Request $httpRequest, Transaction $transaction)
	{
		parent::__construct($httpRequest);
		$this->protocol = $protocol;
		$this->transaction = $transaction;
	}

	/**
	 * Reads the request.
	 * @return Request
	 */
	public function readRequest()
	{
		$request = new SettableRequest();
		$address = $this->protocol->getEndpointRegistry()->getResourceAddress($this->httpRequest->getUri());
		if (is_null($address))
		{
			throw new NotFound($this->httpRequest->getUri(), "No endpoint matching this address was found");
		}
		$request->setResourceAddress($address);
		// TODO Selection
		return $request;
	}

	/**
	 * @return SerializationHelper
	 */
	protected function getSerializationHelper()
	{
		return $this->protocol->getSerializationHelper();
	}
}