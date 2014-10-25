<?php
namespace Light\ObjectService\Resource\Query;

use Light\Util\HTTP\Request as HTTPRequest;

final class UrlScopeParser
{
	/**
	 * @param string	$scopeString
	 * @return Scope
	 */
	public static function parseIntermediateScope($scopeString)
	{
		// TODO
	}

	/**
	 * @param HTTPRequest $httpRequest
	 * @return Scope
	 */
	public static function parseTargetScopeFromRequest(HTTPRequest $httpRequest)
	{
		// TODO
	}

	private function __construct()
	{
		// private constructor
	}
} 