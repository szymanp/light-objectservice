<?php
namespace Szyman\ObjectService\Response;

use Symfony\Component\HttpFoundation\Response;
use Szyman\Exception\UnexpectedValueException;
use Szyman\ObjectService\Configuration\ResponseContentTypeMap;
use Szyman\ObjectService\Service\RequestResult;

/**
 * A {@link RequestResult} that directly creates a {@link Response} object.
 *
 * This class is intended for use together with {@link \Szyman\ObjectService\Service\TransactionHandler::transformResult}.
 */
abstract class CustomRequestResult implements RequestResult
{
	/**
	 * Create a new CustomRequestResult backed by a closure-based implementation of {@link getResponse}.
	 * @param callable $fn	A closure taking a StructureSerializer, DataSerializer and ResponseContentTypeMap,
	 *                    	and returning a Response object.
	 * @return CustomRequestResult
	 */
	public static function newClosure(\Closure $fn)
	{
		return new ClosureCustomRequestResult($fn);
	}

	/**
	 * Returns a response for this request result.
	 *
	 * @param StructureSerializer    $structureSerializer
	 * @param DataSerializer         $dataSerializer
	 * @param ResponseContentTypeMap $responseContentTypeMap
	 * @return Response
	 */
	abstract public function getResponse(StructureSerializer $structureSerializer, DataSerializer $dataSerializer, ResponseContentTypeMap $responseContentTypeMap);
}

/**
 * An implementation of <kbd>CustomRequestResult</kbd> that creates a response by calling a closure.
 */
final class ClosureCustomRequestResult extends CustomRequestResult
{
	private $fn;

	/**
	 * @param callable $fn
	 */
	public function __construct(\Closure $fn)
	{
		$this->fn = $fn;
	}

	/**
	 * @param StructureSerializer    $structureSerializer
	 * @param DataSerializer         $dataSerializer
	 * @param ResponseContentTypeMap $responseContentTypeMap
	 * @return Response
	 */
	public function getResponse(StructureSerializer $structureSerializer, DataSerializer $dataSerializer, ResponseContentTypeMap $responseContentTypeMap)
	{
		$fn = $this->fn;
		$result = $fn($structureSerializer, $dataSerializer, $responseContentTypeMap);
		if (!($result instanceof Response))
		{
			throw UnexpectedValueException::newInvalidReturnValue('closure', 'closure', $result, 'Expecting Response');
		}

		return $result;
	}
}