<?php
namespace Light\ObjectService\Resource\Projection;

use Light\Exception\InvalidParameterType;
use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Resource\ResolvedCollection;
use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Resource\ResolvedCollectionValue;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\ResolvedResource;
use Light\ObjectAccess\Resource\ResolvedScalar;
use Light\ObjectService\Resource\Selection\NestedCollectionSelection;
use Light\ObjectService\Resource\Selection\Selection;

/**
 * Projects a resource object or collection into a DataEntity object according to selection expressions.
 */
class Projector
{
	/**
	 * Projects the value to a DataEntity object.
	 * @param ResolvedResource	$resource
	 * @param Selection 		$selection
	 * @return DataEntity|mixed
	 */
	public function project(ResolvedResource $resource, Selection $selection = null)
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
		else
		{
			throw new InvalidParameterType('$resource', $resource);
		}
	}

	/**
	 * Projects the scalar value.
	 * @param ResolvedScalar $value
	 * @return mixed
	 */
	public function projectScalar(ResolvedScalar $value)
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
	public function projectObject(ResolvedObject $object, Selection $selection = null)
	{
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
			$valueResource = $object->getTypeHelper()->readProperty($object, $fieldName);
			$subselection = $selection->getSubSelection($fieldName);

			$data->$fieldName = $this->project($valueResource, $subselection);
		}

		return $result;

	}

	/**
	 * Projects the collection value.
	 * @param ResolvedCollection $collection
	 * @param Selection          $selection
	 * @return DataCollection
	 */
	public function projectCollection(ResolvedCollection $collection, Selection $selection = null)
	{
		$result = new DataCollection($collection->getTypeHelper(), $collection->getAddress());

		if ($selection && $selection instanceof NestedCollectionSelection && $selection->getScope())
		{
			// The selection provides a Scope. We should therefore apply it.

			// TODO This call could accept the Selection object somehow as a hint for the find() method.
			$elementIterator = $collection->getTypeHelper()->getElements($collection, $selection->getScope());
		}
		elseif ($collection instanceof ResolvedCollectionResource)
		{
			// The collection has no values. There is no scope provided, so we use an empty scope.
			$elementIterator = $collection->getTypeHelper()->getElements($collection, Scope::createEmptyScope());
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

			$value = $this->project($element, $selection);

			if (is_array($data))
			{
				$data[] = $value;
			}
			else
			{
				$data->$key = $value;
			}
		}

		if (is_null($data))
		{
			// The collection was empty. Represent it as an empty array.
			$data = array();
		}

		$result->setData($data);

		return $result;
	}
}

