<?php
namespace Szyman\ObjectService\Request;

use Szyman\ObjectService\Service\ExecutionEnvironment;
use Szyman\ObjectService\Service\RequestHandler;
use Szyman\ObjectService\Service\RequestHandlerFactory;
use Szyman\ObjectService\Service\RequestType;

/**
 * A factory for <kbd>StandardRequestHandler</kbd>.
 */
final class StandardRequestHandlerFactory implements RequestHandlerFactory
{
	/** @var StandardRequestHandler */
	private $requestHandler;

	public function __construct(ExecutionEnvironment $env)
	{
		$this->requestHandler = new StandardRequestHandler($env);
	}

	/**
	 * Creates a new <kbd>RequestHandler</kbd> appropriate for the request type.
	 *
	 * @param RequestType $requestType
	 * @return RequestHandler    A <kbd>RequestHandler</kbd> object, if the factory is capable for producing appropriate
	 *                            handlers; otherwise, NULL.
	 */
	public function newRequestHandler(RequestType $requestType)
	{
		return $this->requestHandler;
	}
}