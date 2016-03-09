<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Type\Type;
use Symfony\Component\HttpFoundation\Request;

/**
 * A factory for objects that can create a HTTP response to a certain type of request.
 */
interface ResponseCreatorFactory
{
	/**
	 * Returns a new ResponseCreator for building a response to a <kbd>resource</kbd> request.
	 *
	 * A resource request is simply a request for reading the resource identified by the request-uri.
	 *
	 * @param Request $request		The HTTP request for which the response will be created.
	 * @param Type    $resourceType	The type of the resource at the request-uri.
	 * @return ResponseCreator
	 */
	public function newResourceResponse(Request $request, Type $resourceType);

	/**
	 * Returns a new ResponseCreator for building a response to a <kbd>creation</kbd> request.
	 *
	 * A creation request can be:
	 * - a PUT request with a request-uri for a resource that does not exist yet; in this case, the resource type
	 *   is a ComplexType
	 * - a POST request where the request-uri resolves to a collection resource; in this case, the resource type
	 *   is a CollectionType, even though the created object can be of another type.
	 *
	 * @param Request $request		The HTTP request for which the response will be created.
	 * @param Type    $resourceType	The type of the resource at the request-uri.
	 * @return ResponseCreator
	 */
	public function newCreationResponse(Request $request, Type $resourceType);

	/**
	 * Returns a new ResponseCreator for building a response to a <kbd>modification</kbd> request.
	 *
	 * A modification request can be:
	 * - a PATCH request with a request-uri identifying a ComplexType resource,
	 * - a PATCH request with a request-uri identifying a CollectionType resource.
	 *
	 * @param Request $request		The HTTP request for which the response will be created.
	 * @param Type    $resourceType	The type of the resource at the request-uri.
	 * @return ResponseCreator
	 */
	public function newModificationResponse(Request $request, Type $resourceType);

	/**
	 * Returns a new ResponseCreator for building a response to a <kbd>replacement</kbd> request.
	 *
	 * A replacement request is a PUT request with a request-uri that identifies a SimpleType, ComplexType
	 * or CollectionType resource.
	 *
	 * @param Request $request		The HTTP request for which the response will be created.
	 * @param Type    $resourceType	The type of the resource at the request-uri.
	 * @return ResponseCreator
	 */
	public function newReplacementResponse(Request $request, Type $resourceType);

	/**
	 * Returns a new ResponseCreator for building a response to a <kbd>deletion</kbd> request.
	 *
	 * A deletion request is a DELETE request with a request-uri that identifies a ComplexType or a CollectionType
	 * resource. Note that in the latter case, the deletion pertains not to the collection itself, but to the certain
	 * elements in the collection.
	 *
	 * @param Request $request		The HTTP request for which the response will be created.
	 * @param Type    $resourceType	The type of the resource at the request-uri.
	 * @return ResponseCreator
	 */
	public function newDeletionResponse(Request $request, Type $resourceType);

	/**
	 * Returns a new ResponseCreator for building a response to an <kbd>action</kbd> request.
	 *
	 * An action request is a POST request with a request-uri that identifies a ComplexType or a CollectionType
	 * resource.
	 *
	 * @param Request $request		The HTTP request for which the response will be created.
	 * @param Type    $resourceType	The type of the resource at the request-uri.
	 * @return ResponseCreator
	 */
	public function newActionResponse(Request $request, Type $resourceType);
}