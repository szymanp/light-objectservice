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
	/**
	 * @var StandardRequestHandlerFactory
	 */
	private static $INSTANCE = null;

	/**
	 * Returns an instance of this factory.
	 * @return StandardRequestHandlerFactory
	 */
	public static function getInstance()
	{
		if (is_null(self::$INSTANCE)) self::$INSTANCE = new self;
		return self::$INSTANCE;
	}

	/**
	 * Creates a new <kbd>RequestHandler</kbd> appropriate for the request type.
	 *
	 * @param RequestType $requestType
	 * @param ExecutionEnvironment $environment
	 * @return RequestHandler    A <kbd>RequestHandler</kbd> object, if the factory is capable for producing appropriate
	 *                            handlers; otherwise, NULL.
	 */
	public function newRequestHandler(RequestType $requestType, ExecutionEnvironment $environment)
	{
		return new StandardRequestHandler($environment);
	}
}