<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Symfony\Component\HttpFoundation\Request;
use Light\ObjectAccess\Resource\ResolvedResource;

/**
 * An interface for classes that can handle REST requests.
 */
interface RequestHandler
{
	/**
	 * Handle a request.
	 *
	 * @param Request			$request			The HTTP request object.
	 * @param RequestComponents	$requestComponents
	 * @return RequestResult
	 */
	public function handle(Request $request, RequestComponents $requestComponents);
}
