<?php
namespace Light\ObjectService\Protocol;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Json\Response\DefaultExceptionSerializer;
use Light\ObjectService\Json\Response\DefaultSerializer;
use Light\ObjectService\Service\EndpointRegistry;
use Light\ObjectService\Service\Protocol\AcceptResult;
use Light\ObjectService\Service\Protocol\Protocol;
use Light\ObjectService\Service\Protocol\SerializationHelper;
use Symfony\Component\HttpFoundation\Request;

class SimpleGetProtocol implements Protocol
{
	/** @var SerializationHelper */
	private $serializationHelper;
	/** @var EndpointRegistry */
	private $endpointRegistry;

	public function __construct()
	{
		$this->serializationHelper = new SerializationHelper();
		$this->serializationHelper->addSerializer(new DefaultSerializer());
		$this->serializationHelper->addSerializer(new DefaultExceptionSerializer());
	}

	/**
	 * @return EndpointRegistry
	 */
	public function getEndpointRegistry()
	{
		return $this->endpointRegistry;
	}

	/**
	 * @return SerializationHelper
	 */
	public function getSerializationHelper()
	{
		return $this->serializationHelper;
	}

	/**
	 * Configures the protocol to use the specified EndpointRegistry.
	 * @param EndpointRegistry $endpointRegistry
	 */
	public function configure(EndpointRegistry $endpointRegistry)
	{
		$this->endpointRegistry = $endpointRegistry;
	}

	/**
	 * @inheritdoc
	 */
	public function accepts(Request $httpRequest)
	{
		if ($httpRequest->getMethod() != "GET")
		{
			return AcceptResult::badMethod(array("GET"));
		}
		if ($this->serializationHelper->getResourceSerializer($httpRequest) !== null)
		{
			return AcceptResult::badRequestedContentType($this->serializationHelper->getSerializableContentTypes());
		}
		return AcceptResult::accepted();
	}

	/**
	 * Returns a new instance of this protocol.
	 * @param Request     $httpRequest
	 * @param Transaction $transaction
	 * @return SimpleGetProtocolInstance
	 */
	public function newInstance(Request $httpRequest, Transaction $transaction)
	{
		return new SimpleGetProtocolInstance($this, $httpRequest, $transaction);
	}
}