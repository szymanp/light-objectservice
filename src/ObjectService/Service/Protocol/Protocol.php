<?php
namespace Light\ObjectService\Service\Protocol;

use Symfony\Component\HttpFoundation\Request;

/**
 * Checks if the protocol is applicable to a given request.
 */
interface Protocol
{
	/**
	 * Returns true if this protocol can handle the given request.
	 * @param Request $httpRequest
	 * @return boolean
	 */
	public function accepts(Request $httpRequest);

	/**
	 * Returns a new instance of this protocol.
	 * @param Request $httpRequest
	 * @return
	 */
	public function newInstance(Request $httpRequest);
}