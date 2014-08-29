<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Type\ResolvedValue;
use Light\ObjectService\Exceptions\InvalidRequestException;
use Light\ObjectService\Resource\Operation\Operation;
use Light\ObjectService\Resource\Operation\CreateOperation;
use Light\ObjectService\Resource\Operation\UpdateOperation;
use Light\ObjectService\Resource\Operation\ReadOperation;
use Light\Exception\Exception;
use Light\ObjectService\Expression\PathExpression;
use Light\Exception\NotImplementedException;

/**
 * Base for classes that parse JSON into Operation objects. 
 *
 */
abstract class JsonOperationReader
{
	/**
	 * Creates a new reader for reading the root-level Operation.
	 * @param string			$method
	 * @param PathExpression 	$resourcePath
	 * @param \stdClass 		$data
	 * @param \stdClass 		$meta
	 * @return \Light\ObjectService\Service\Json\JsonOperationReader
	 */
	public static function createRoot($method, PathExpression $resourcePath, \stdClass $data = null, \stdClass $meta = null)
	{
		// We pass in a PathExpression and the JsonRequestReader will put an actual ResolvedValue
		// on the root operation.
		return self::create($method, $resourcePath, $data, $meta);
	}
	
	/**
	 * Creates a new reader for reading a child-level Operation.
	 * @param Operation $parent
	 * @param \stdClass $operationRequest
	 * @return \Light\ObjectService\Service\Json\JsonOperationReader
	 */
	public static function createChild(Operation $parent, \stdClass $operationRequest)
	{
		if (isset($operationRequest->method))
		{
			$method = $operationRequest->method;
		}
		else
		{
			$method = self::getMethodFromOperation($parent);
		}
		
		$data = isset($operationRequest->data) ? $operationRequest->data : null;
		$meta = isset($operationRequest->meta) ? $operationRequest->meta : null;
		
		if ($operationRequest->href)
		{
			$resourcePath = JsonPathExpressionReader::read($operationRequest->href, $operationRequest->query, $parent);
		}
		else
		{
			$resourcePath = $parent->getResourcePath();
		}
		
		return self::create($method, $resourcePath, $data, $meta, $parent);
	}

	/**
	 * @return \Light\ObjectService\Service\Json\JsonOperationReader
	 */
	private static function create($method, PathExpression $resourcePath, \stdClass $data = null, \stdClass $meta = null, Operation $parent = null)
	{
		switch ($method)
		{
			case "GET":
				return new JsonReadOperationReader($resourcePath, $data, $meta, $parent);
			case "POST":
				return new JsonCreateOperationReader($resourcePath, $data, $meta, $parent);
			case "PUT":
				return new JsonUpdateOperationReader($resourcePath, $data, $meta, $parent);
			case "DELETE":
				throw new NotImplementedException();
			case "ACTION":
				throw new NotImplementedException();
			case "TRANSACTION":
				return new JsonTransactionOperationReader($resourcePath, $data, $meta, $parent);
			default:
				throw new InvalidRequestException("Invalid method \"%1\"", $method);
		}
	}
	
	/**
	 * Returns the method corresponding to an operation.
	 * @param Operation $oper
	 * @return string
	 */
	private static function getMethodFromOperation(Operation $oper)
	{
		if ($oper instanceof CreateOperation)
		{
			return "POST";
		}
		else if ($oper instanceof UpdateOperation)
		{
			return "PUT";
		}
		else if ($oper instanceof ReadOperation)
		{
			return "GET";
		}
		else
		{
			// TODO
			throw new Exception("Unknown operation type \"%1\%", get_class($oper));
		}
	}
	
	/** @var \Light\ObjectService\Resource\Operation\Operation */
	protected $parentOperation;
	/** @var \Light\ObjectService\Expression\PathExpression */
	protected $resourcePath;
	/** @var \stdClass|NULL */
	protected $data;
	/** @var \stdClass|NULL */
	protected $meta;
	
	public function __construct(PathExpression $resourcePath, \stdClass $data = null, \stdClass $meta = null, Operation $parent = null)
	{
		$this->parentOperation	= $parent;
		$this->resourcePath 	= $resourcePath;
		$this->data 			= $data;
		$this->meta 			= $meta;
		
		$this->prevalidate();
	}
	
	/**
	 * Adds a parent operation and resource path to the specified operation object.
	 * @param Operation $oper
	 */
	final protected function setupOperation(Operation $oper)
	{
		if ($this->parentOperation)
		{
			$oper->setParent($this->parentOperation);
		}
		$oper->setResourcePath($this->resourcePath);
	}
	
	/**
	 * Perform initial validation 
	 */
	abstract protected function prevalidate();
	
	/**
	 * @return \Light\ObjectService\Resource\Operation\Operation
	 */
	abstract public function read();
}
