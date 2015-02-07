<?php
namespace Light\ObjectService\Service\Protocol;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Service\EndpointRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * Checks if the protocol is applicable to a given request.
 */
interface Protocol
{
	/**
	 * Configures the protocol to use the specified EndpointRegistry.
	 * @param EndpointRegistry $endpointRegistry
	 */
	public function configure(EndpointRegistry $endpointRegistry);

	/**
	 * Checks whether this protocol can handle the given request.
	 * @param Request $httpRequest
	 * @return AcceptResult
	 */
	public function accepts(Request $httpRequest);

	/**
	 * Returns a new instance of this protocol to handle a given request.
	 * @param Request 		$httpRequest
	 * @param Transaction 	$transaction	The transaction associated with this request.
	 * @return ProtocolInstance
	 */
	public function newInstance(Request $httpRequest, Transaction $transaction);
}