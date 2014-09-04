<?php
namespace Light\ObjectService\Service\Response;

use Light\Exception\InvalidParameterType;
use Light\ObjectService\Expression\FindContextObject;
use Light\ObjectService\Expression\NestedSelectExpression;
use Light\ObjectService\Expression\SelectExpression;
use Light\ObjectService\Expression\WhereExpression;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Type\CollectionType;
use Light\ObjectService\Type\ComplexType;
use Light\ObjectService\Type\ObjectProvider;
use Light\ObjectService\Type\SimpleType;
use Light\ObjectService\Type\Type;
use Light\ObjectService\Type\TypeHelper;

/**
 * Projects a resource object or collection into a DataEntity object according to selection expressions.
 *
 */
abstract class Projector
{
	/** @var \Light\ObjectService\ObjectRegistry */
	protected $registry;
	
	/**
	 * Returns a new Projector instance.
	 * @param ObjectRegistry 	$registry
	 * @param Type 				$type
	 * @return \Light\ObjectService\Service\Response\Projector
	 */
	public static function create(ObjectRegistry $registry, Type $type)
	{
		if ($type instanceof ComplexType)
		{
			return new Projector_Complex($registry, $type);
		}
		else if ($type instanceof SimpleType)
		{
			return new Projector_Simple($registry, $type);
		}
		else if ($type instanceof CollectionType)
		{
			return new Projector_Collection($registry, $type);
		}
		else
		{
			throw new InvalidParameterType('$type', $type);
		}
	}
	
	protected function __construct(ObjectRegistry $registry)
	{
		$this->registry = $registry;
	}
	
	/**
	 * Projects the value to a DataEntity object.
	 * @param mixed				$value
	 * @param SelectExpression	$select
	 * @return \Light\ObjectService\Service\Response\DataEntity|mixed
	 */
	abstract public function project($value, SelectExpression $select = null);
}

/**
 * Projects an object (with properties) into a DataObject.
 * 
 */
final class Projector_Complex extends Projector
{
	/** @var \Light\ObjectService\Type\ComplexType */
	private $type;
	/** @var \Light\ObjectService\Type\TypeHelper */
	private $typeHelper;
	
	protected function __construct(ObjectRegistry $registry, ComplexType $type)
	{
		parent::__construct($registry);
		$this->type = $type;
		$this->typeHelper = new TypeHelper($registry);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Response\Projector::project()
	 */
	public function project($object, SelectExpression $select = null)
	{
		if (is_null($select))
		{
			$select = $this->type->getDefaultSelection();
		}
		
		$spec	= $this->type->getSpecification();
		$result = new DataObject($this->type);
		$data	= $result->getData();
		$fields = $select->getFields();
		
		foreach($fields as $field)
		{
			$subselect = $select->getSubselect($field);
			$fieldspec = $spec->getFieldOrThrow($field);
			
			if (!is_null($objectProvider = $this->typeHelper->getObjectProviderOrNull($fieldspec)))
			{
				// If the field's type is an object provider, then instead of reading it directly,
				// we use the provider to fetch the data. 
				$value = $this->readValueFromObjectProvider($objectProvider, $object, $subselect);
			}
			else
			{
				$value 	= $this->type->readProperty($object, $field);
			}
			
			$fieldtype = $this->typeHelper->getTypeForField($fieldspec);
			$valueProjector = Projector::create($this->registry, $fieldtype);
			$data->$field = $valueProjector->project($value, $subselect);
		}
		
		return $result;
	}
	
	private function readValueFromObjectProvider(ObjectProvider $provider, $object, NestedSelectExpression $select = null)
	{
		$context = new FindContextObject();
		$context->setContextObject($object);
		
		$whereExpr = null;
		if ($select)
		{
			$whereExpr = $select->getWhereExpression();
		}
		if (is_null($whereExpr))
		{
			$whereExpr = WhereExpression::create($provider);
		}
		
		$result = $provider->find($whereExpr, $context);
		
		// TODO Type conversion of result?
		
		return $result;
	}
}

/**
 * Converts an object, array or a scalar into another type/format.
 *
 */
final class Projector_Simple extends Projector
{
	/** @var \Light\ObjectService\Type\SimpleType */
	private $type;

	protected function __construct(ObjectRegistry $registry, SimpleType $type)
	{
		parent::__construct($registry);
		$this->type = $type;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Response\Projector::project()
	 */
	public function project($value, SelectExpression $select = null)
	{
		// todo Type conversion if SimpleType implements one
		
		return $value;
	}
}

/**
 * Converts a Traversable value into a simple list. 
 *
 */
final class Projector_Collection extends Projector
{
	/** @var \Light\ObjectService\Type\CollectionType */
	private $type;

	protected function __construct(ObjectRegistry $registry, CollectionType $type)
	{
		parent::__construct($registry);
		$this->type = $type;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Light\ObjectService\Service\Response\Projector::project()
	 */
	public function project($coll, SelectExpression $select = null)
	{
		// todo
		// What should we do here?
		// We could let the CollectionType decide whether it wants to be serialized as an object or as an array.
		
		$result = new DataCollection($this->type);
		$valueProjector = Projector::create($this->registry, $this->type->getBaseType());

		$key = key($coll);
		if (is_int($key) || is_null($key))
		{
			// The array has numeric keys - represent it as an array
			$prj = array();
			foreach($coll as $elem)
			{
				$prj[] = $valueProjector->project($elem, $select);
			}
		}
		else
		{
			// The array has string keys - represent it as an object
			$prj = new \stdClass;
			foreach($coll as $key => $elem)
			{
				$prj->$key = $valueProjector->project($elem, $select);
			}
		}
		
		$result->setData($prj);
		
		return $result;
	}
}
