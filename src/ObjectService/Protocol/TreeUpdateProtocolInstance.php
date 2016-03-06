<?php
namespace Light\ObjectService\Protocol;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Resource\Util\DefaultExecutionEnvironment;
use Light\ObjectService\Service\Protocol\SerializationHelper;
use Light\ObjectService\Service\Request;
use Light\ObjectService\Service\Util\SettableRequest;
use Symfony\Component\HttpFoundation;

class TreeUpdateProtocolInstance extends AbstractProtocolInstance
{
	/** @var TreeUpdateProtocol */
	private $protocol;

	public function __construct(TreeUpdateProtocol $protocol, HttpFoundation\Request $httpRequest, Transaction $transaction)
	{
		parent::__construct($httpRequest, $transaction);
		$this->protocol = $protocol;
	}

	/**
	 * Reads the request.
	 * @return Request
	 */
	public function readRequest()
	{
		$request = new SettableRequest();
		$request->setResourceAddress($address = $this->readResourceAddress($this->protocol->getEndpointRegistry()));

		if (!is_null($selection = $this->readSelection()))
		{
			$request->setSelection($selection);
		}

		$executionParameters = new DefaultExecutionEnvironment();
		$executionParameters->setEndpointRegistry($this->protocol->getEndpointRegistry());
		$executionParameters->setEndpoint($address->getEndpoint());
		$executionParameters->setTransaction($this->transaction);

		$deserializer = $this->getSerializationHelper()->getDeserializer($this->httpRequest);
		$request->setFromDeserializedResult($deserializer->deserialize($this->httpRequest, $executionParameters));

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