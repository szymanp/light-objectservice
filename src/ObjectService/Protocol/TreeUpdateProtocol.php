<?php
namespace Light\ObjectService\Protocol;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Formats\Json\Deserializers\SimpleTreeDeserializer;
use Light\ObjectService\Formats\Json\Serializers\DefaultExceptionSerializer;
use Light\ObjectService\Formats\Json\Serializers\DefaultSerializer;
use Light\ObjectService\Service\EndpointRegistry;
use Light\ObjectService\Service\Protocol\AcceptResult;
use Light\ObjectService\Service\Protocol\Protocol;
use Light\ObjectService\Service\Protocol\ProtocolInstance;
use Light\ObjectService\Service\Protocol\SerializationHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * A protocol for updating the object structure.
 */
class TreeUpdateProtocol implements Protocol
{
	/** @var EndpointRegistry */
	private $endpointRegistry;
	/** @var SerializationHelper */
	private $serializationHelper;

	public function __construct()
	{
		$this->serializationHelper = new SerializationHelper();
		$this->serializationHelper->addSerializer(new DefaultSerializer());
		$this->serializationHelper->addSerializer(new DefaultExceptionSerializer());

		$this->serializationHelper->addDeserializer(new SimpleTreeDeserializer());

		// Fallback for text/json
		$this->serializationHelper->addSerializer(new DefaultSerializer("text/json"));
		$this->serializationHelper->addSerializer(new DefaultExceptionSerializer(false, "text/json"));
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
	 * Checks whether this protocol can handle the given request.
	 * @param Request $httpRequest
	 * @return AcceptResult
	 */
	public function accepts(Request $httpRequest)
	{
		$methods = ["POST", "PUT", "DELETE"];

		if (!in_array($httpRequest->getMethod(), $methods))
		{
			return AcceptResult::badMethod($methods);
		}
		if ($this->serializationHelper->getResourceSerializer($httpRequest) === null)
		{
			return AcceptResult::badRequestedContentType($this->serializationHelper->getSerializableContentTypes());
		}
		if ($this->serializationHelper->getDeserializer($httpRequest) === null)
		{
			return AcceptResult::badSuppliedContentType($this->serializationHelper->getDeserializableContentTypes());
		}
		return AcceptResult::accepted();
	}

	/**
	 * Returns a new instance of this protocol to handle a given request.
	 * @param Request     $httpRequest
	 * @param Transaction $transaction The transaction associated with this request.
	 * @return ProtocolInstance
	 */
	public function newInstance(Request $httpRequest, Transaction $transaction)
	{
		return new TreeUpdateProtocolInstance($this, $httpRequest, $transaction);
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
}