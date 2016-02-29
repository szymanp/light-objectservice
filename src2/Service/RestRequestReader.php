<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Query\Query;
use Light\ObjectAccess\Query\Scope;
use Light\ObjectService\Exception\MethodNotAllowed;
use Light\ObjectService\Exception\NotFound;
use Light\ObjectService\Service\EndpointRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * Reads a HTTP Request according to REST rules.
 */
class RestRequestReader
{
	/** @var EndpointRegistry */
	private $endpointRegistry;

	public function readRequest(Request $request)
	{
		$address = $this->getResourceAddress($request);

		switch($request->getMethod())
		{
			case 'GET':
				// Read the resource at URL. The resource MUST exist.
				// There is no body, so the body content-type does not matter.

				break;
			case 'PUT':
				// Create or replace a resource specified at URL. The resource MAY NOT exist.
				// The body content-type MUST be in a "update" format.
				// The underlying resource may be an object or a collection.
				break;
			case 'PATCH':
				// Update a resource at the specified URL. The resource MUST exist.
				// The body content-type MUST be in a "update" format.
				// The underlying resource may be an object or a collection.

				// Procedure:
				// Find the EndpointRelativeAddress for the URL.
				// Read the RelativeAddress corresponding to this EndpointRelativeAddress.
				// (The RelativeAddress contains a published resource and the path to the target resource).
				// Resolve the RelativeAddress to the target resource using RelativeAddressReader.
				// The resolution must be successful, i.e. not return a NULL.

				// If the resource is a collection, then parse the request-body using a Update Collection format parser.
				// If the resource is a resource, then parse the request-body using a Update Object format parser.

				// The parsing returns an Operation object.
				// Execute the Operation object on the resource.

				// Find the


				break;
			case 'DELETE':
				// Delete the resource at the specified URL. The resource MUST exist.
				// The body is optional. It can contain extra arguments for the deletion,
				// such as the scope of elements to be deleted from a collection.
				// What should be the content-type of the body?
				break;
			case 'POST':
				// Post can execute one of the following actions:
				// - If content-type of the body is in "update" format, then it adds a resource to a collection.
				//   The resource identified by the URL MUST exist and MUST be a collection.
				// - If content-type of the body is in an "action" format, then the specified action is executed.
				//	 The underlying resource MUST exist and may be either an object or a collection.
				break;
			default:
				throw new MethodNotAllowed();
		}
	}

	/**
	 * Returns the URI without the query section.
	 * @param Request $request
	 * @return string
	 */
	private function getUriWithoutQuery(Request $request)
	{
		$uri = $request->getUri();

		// Remove the query section
		if (($qm = strpos($uri, '?')) > -1)
		{
			return substr($uri, 0, $qm);
		}
		else
		{
			return $uri;
		}
	}

	private function getResourceAddress(Request $request)
	{
		$address = $this->endpointRegistry->getResourceAddress($this->getUriWithoutQuery($request));

		if (is_null($address))
		{
			throw new NotFound($request->getUri(), "No endpoint matching this address was found");
		}

		if ($request->query->has("count") || $request->query->has("offset"))
		{
			$count  = $request->query->getInt("count", null);
			$offset = $request->query->getInt("offset", null);
			$limitScope = Scope::createWithQuery(Query::emptyQuery(), $count, $offset);
			$address = $address->appendScope($limitScope);
		}

		return $address;
	}
}