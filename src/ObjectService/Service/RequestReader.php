<?php
namespace Light\ObjectService\Service;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Exception\MalformedRequest;
use Symfony\Component\HttpFoundation;

interface RequestReader
{
	/**
	 * Returns true if the HTTP Request can be processed by this Request Reader.
	 * @param HttpFoundation\Request $httpRequest
	 * @return boolean
	 */
	public function isAcceptable(HttpFoundation\Request $httpRequest);

	/**
	 * Returns a list of content-types that are acceptable by this Request Reader.
	 * @return string[]
	 */
	public function getAcceptableContentTypes();

	/**
	 * Parse a HTTP Request and return a Service Request object.
	 * @param HttpFoundation\Request 	$httpRequest
	 * @param EndpointRegistry 			$endpointRegistry
	 * @param Transaction 				$transaction
	 * @throws MalformedRequest
	 * @return Request
	 */
	public function read(HttpFoundation\Request $httpRequest, EndpointRegistry $endpointRegistry, Transaction $transaction);
}