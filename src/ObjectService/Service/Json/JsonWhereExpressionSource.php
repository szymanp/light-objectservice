<?php
namespace Light\ObjectService\Service\Json;

use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\Model\CollectionType;
use Light\ObjectService\Expression\WhereExpressionSource;
use Light\ObjectService\Model\Type;
use Light\ObjectService\Model\TypeHelper;
use Light\ObjectService\Model\ComplexType;
use Light\ObjectService\Expression\Criterion;
use Light\ObjectService\Exceptions\InvalidRequestException;
use Light\Exception\NotImplementedException;

/**
 * Reads a Where Expression in JSON format.
 * 
 * A where expression in JSON has the following format:
 * 
 * {
 *  field: "aa",
 *  field2: [1, 2, 3]
 *  field3: { eq: [12, 13] },
 *  instanceof: [ {} ]
 * }
 * 
 *
 */
final class JsonWhereExpressionSource implements WhereExpressionSource
{
	private $data;
	
	/**
	 * Creates a new JsonWhereExpressionSource object.
	 * @param \stdClass $data
	 * @return \Light\ObjectService\Service\Json\JsonWhereExpressionSource
	 */
	public static function create(\stdClass $data)
	{
		$src = new self;
		$src->data = $data;
		return $src;
	}
	
	private function __construct()
	{
		// a private constructor
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Expression\WhereExpressionSource::compile()
	 */
	public function compile(CollectionType $type)
	{
		$whereExpr = WhereExpression::create($type);
		$spec = $type->getSpecification();
		$typeHelper = TypeHelper::create($type->getObjectRegistry());
		
		foreach($this->data as $name => $value)
		{
			$fieldSpec = $spec->getFieldOrThrow($name);
			$type = $typeHelper->getTypeForField($fieldSpec);
			
			if ($fieldSpec->isCriterion())
			{
				$this->parseCriteria($whereExpr, $name, $value, $type);
			}
			elseif ($type instanceof ComplexType)
			{
				$this->parseResourceRef($whereExpr, $name, $value, $type);
			}
			elseif ($type instanceof CollectionType
					&& $type->getBaseType() instanceof ComplexType)
			{
				$this->parseResourceRefs($whereExpr, $name, $value, $type);
			}
			elseif ($type instanceof CollectionType)
			{
				$this->parseValues($whereExpr, $name, $value, $type);
			}
			else
			{
				$this->parseValue($whereExpr, $name, $value, $type);
			}
		}
		
		return $whereExpr;
	}
	
	/**
	 * Parses a criterion.
	 * 
	 * A criterion can have one of the following forms:
	 * <code>
	 * {
	 *  criterion1: <simple-value>,	// assume "equality"
	 *  criterion2: { <criterion-type>: <simple-value> },
	 *  criterion3: { <criterion-type>: <simple-values> }
	 * }
	 * </code>
	 * 
	 * @param WhereExpression 	$expr
	 * @param string 			$name
	 * @param mixed 			$value
	 * @param Type 				$type
	 */
	private function parseCriteria(WhereExpression $expr, $name, $value, Type $type)
	{
		if (is_scalar($value))
		{
			$expr->setValue($name, new Criterion($value));
		}
		else if (is_object($value))
		{
			$operators = array(
				"eq" => Criterion::EQ, 
				"gt" => Criterion::GT, 
				"lt" => Criterion::LT,
				"like" => Criterion::LIKE);
			
			foreach($operators as $operator => $operatorKey)
			{
				if (!isset($value->$operator)) continue;
				
				$operValue = $value->$operator;
				if (is_array($operValue))
				{
					foreach($operValue as $v)
					{
						$expr->setValue($name, new Criterion($v, $operatorKey));
					}
				}
				else
				{
					$expr->setValue($name, new Criterion($operValue, $operatorKey));
				}
			}
		}
		else
		{
			throw new InvalidRequestException("Cannot parse criterion \"%1\"", $name);
		}
	}

	private function parseResourceRef(WhereExpression $expr, $name, $value, Type $type)
	{
		throw new NotImplementedException();
	}
	
	private function parseResourceRefs(WhereExpression $expr, $name, $value, Type $type)
	{
		throw new NotImplementedException();
	}
	
	private function parseValues(WhereExpression $expr, $name, $value, Type $type)
	{
		throw new NotImplementedException();
	}
	
	private function parseValue(WhereExpression $expr, $name, $value, Type $type)
	{
		throw new NotImplementedException();
	}
}