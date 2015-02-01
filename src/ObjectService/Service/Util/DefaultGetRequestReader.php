<?php
namespace Light\ObjectService\Service\Util;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectService\Exception\MalformedRequest;
use Light\ObjectService\Service\EndpointRegistry;
use Light\ObjectService\Service\Request;
use Light\ObjectService\Service\RequestReader;
use Symfony\Component\HttpFoundation;

class DefaultGetRequestReader implements RequestReader
{
	/**
	 * @inheritdoc
	 */
	public function isAcceptable(HttpFoundation\Request $httpRequest)
	{
		return $httpRequest->getMethod() === "GET";
	}

	/**
	 * @inheritdoc
	 */
	public function getAcceptableContentTypes()
	{
		return array();
	}

	/**
	 * @inheritdoc
	 */
	public function read(HttpFoundation\Request $httpRequest, EndpointRegistry $endpointRegistry, Transaction $transaction)
	{
		$request = new SettableRequest();
		$request->setResourceAddress($endpointRegistry->getResourceAddress($httpRequest->getUri()));
		// TODO Selection
		return $request;
	}
}