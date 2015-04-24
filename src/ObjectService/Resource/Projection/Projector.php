<?php
namespace Light\ObjectService\Resource\Projection;

use Light\Exception\InvalidParameterType;
use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Resource\ResolvedCollection;
use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Resource\ResolvedCollectionValue;
use Light\ObjectAccess\Resource\ResolvedNull;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedScalar;
use Light\ObjectService\Resource\Selection\NestedCollectionSelection;
use Light\ObjectService\Resource\Selection\Selection;
use Light\ObjectService\Resource\Util\DefaultSearchContext;

/**
 * Projects a resource object or collection into a DataEntity object according to selection expressions.
 */
class Projector
{
	/**
	 * Holds a list of objects that have been projected to avoid infinite loops.
	 * @var array
	 */
	private $traversedObjects = array();
	/** @var integer */
	private $traversalIdSequence = 1;
	/** @var array */
	private $stack = array();

	/**
	 * Projects the value to a DataEntity object.
	 * @param ResolvedResource $resource
	 * @param Selection        $selection
	 * @return DataEntity|mixed
	 * @throws ProjectorException
	 */
	public function project(ResolvedResource $resource, Selection $selection = null)
	{
		try
		{
			if ($resource instanceof ResolvedScalar)
			{
				return $this->projectScalar($resource);
			}
			elseif ($resource instanceof ResolvedObject)
			{
				return $this->projectObject($resource, $selection);
			}
			elseif ($resource instanceof ResolvedCollection)
			{
				return $this->projectCollection($resource, $selection);
			}
			elseif ($resource instanceof ResolvedNull)
			{
				return null;
			}
			else
			{
				throw new InvalidParameterType('$resource', $resource);
			}
		}
		catch (ProjectorException $e)
		{
			throw $e;
		}
		catch (\Exception $e)
		{
			throw new ProjectorException($this->stack, $e);
		}
	}

	/**
	 * Projects the scalar value.
	 * @param ResolvedScalar $value
	 * @return mixed
	 */
	protected function projectScalar(ResolvedScalar $value)
	{
		// TODO We could invoke some type conversion from the SimpleType class...

		return $value->getValue();
	}

	/**
	 * Projects the object value.
	 * @param ResolvedObject $object
	 * @param Selection      $selection
	 * @return DataObject
	 * @throws \Exception
	 */
	protected function projectObject(ResolvedObject $object, Selection $selection = null)
	{
		if ($traversalId = $this->traverse($object->getValue()) === true)
		{
			return new DataObject($object->getTypeHelper(), $object->getAddress());
		}

		if (is_null($selection))
		{
			// TODO Get default selection from the type.
			// For now, we select all fields.
			$selection = Selection::create($object->getTypeHelper())->fields("*");
		}

		$result = new DataObject($object->getTypeHelper(), $object->getAddress());
		$data = $result->getData();

		foreach($selection->getFields() as $fieldName)
		{
			$this->stack[] = $fieldName;

			$valueResource = $object->getTypeHelper()->readProperty($object, $fieldName);
			$subselection = $selection->getSubSelection($fieldName);

			$data->$fieldName = $this->project($valueResource, $subselection);

			array_pop($this->stack);
		}

		$this->finishTraversal($traversalId);

		return $result;

	}

	/**
	 * Projects the collection value.
	 * @param ResolvedCollection $collection
	 * @param Selection          $selection
	 * @return DataCollection
	 */
	protected function projectCollection(ResolvedCollection $collection, Selection $selection = null)
	{
		$result = new DataCollection($collection->getTypeHelper(), $collection->getAddress());

		if ($collection instanceof ResolvedCollectionResource)
		{
			$scope = null;
			if ($selection && $selection instanceof NestedCollectionSelection)
			{
				$scope = $selection->getScope();
			}
			$scope = is_null($scope) ? Scope::createEmptyScope() : $scope;

			if ($scope instanceof Scope\QueryScope)
			{
				$searchContext = new DefaultSearchContext();
				$searchContext->setSelectionHint($selection);
				$elementIterator = $collection->getTypeHelper()->getIterator($collection->getTypeHelper()->queryCollection($collection, $scope, $searchContext));
			}
			else
			{
				$elementIterator = $collection->getTypeHelper()->getIteratorWithScope($collection, $scope);
			}
		}
		elseif ($collection instanceof ResolvedCollectionValue)
		{
			// The collection has values, we fetch them via a call to getIterator().
			$elementIterator = $collection->getTypeHelper()->getIterator($collection);
		}
		else
		{
			throw new InvalidParameterType('$collection', $collection);
		}

		$data = null;

		foreach($elementIterator as $key => $element)
		{
			if (is_null($data))
			{
				// This is the first iteration. We need to determine whether the collection
				// is indexed with integer keys starting from zero or if it is a dictionary.
				if ($key === 0)
				{
					$data = array();
				}
				else
				{
					$data = new \stdClass();
				}
			}

			$this->stack[] = "[" . $key . "]";

			$value = $this->project($element, $selection);

			if (is_array($data))
			{
				$data[] = $value;
			}
			else
			{
				$data->$key = $value;
			}

			array_pop($this->stack);
		}

		if (is_null($data))
		{
			// The collection was empty. Represent it as an empty array.
			$data = array();
		}

		$result->setData($data);

		return $result;
	}

	/**
	 * @param object $phpObject
	 * @return bool|int    	Returns true if the object has already been traversed;
	 * 						otherwise, returns an ID of the traversal for later removal with {@link finishTraversal}.
	 */
	final protected function traverse($phpObject)
	{
		if (in_array($phpObject, $this->traversedObjects, true))
		{
			return true;
		}
		else
		{
			$id = $this->traversalIdSequence++;
			$this->traversedObjects[$id] = $phpObject;
			return $id;
		}
	}

	final protected function finishTraversal($traversalId)
	{
		unset($this->traversedObjects[$traversalId]);
	}
}

