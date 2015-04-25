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

	public function __construct(SimpleGetProtocol $protocol, HttpFoundation\Request $httpRequest, Transaction $transaction)
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
		$request->setResourceAddress($this->readResourceAddress($this->protocol->getEndpointRegistry()));

		if (!is_null($selection = $this->readSelection()))
		{
			$request->setSelection($selection);
		}

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