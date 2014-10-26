<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectService\Service\Endpoint;

final class ResolvedResourceIdentifier extends ResourceIdentifier
{
	/**
	 * The service endpoint that this resource belongs to.
	 * @var Endpoint
	 */
	private $endpoint;

	/** @var ResourcePath */
	private $resourcePath;

	/** @var boolean */
	private $openCollection;

	public function __construct()
	{

	}

	public function getBaseResource()
	{
		// TODO
	}

	public function getResourcesInScope()
	{
		// TODO
	}
} 