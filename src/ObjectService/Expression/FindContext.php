<?php

namespace Light\ObjectService\Expression;

/**
 * Provides additional information for the retrieval of objects by an ObjectProvider.
 */
interface FindContext
{
	/**
	 * Returns the related object for the query.
	 * 
	 * An object to be retrieved can be accessed via a property of another object.
	 * In this case, the object on which the property is defined is called the "related" object.
	 * 
	 * @return object	An object, if there is one; otherwise, NULL.
	 */
	public function getContextObject();
	
	/**
	 * Returns the selection expression that will be used for projecting data from the objects.
	 * 
	 * When reading data from a database it is useful to know what fields are needed
	 * to optimize the query. This information can be obtained from the selection expression
	 * 
	 * @return \Light\ObjectService\Expression\SelectExpression
	 * 					A selection expression, if defined; otherwise, NULL.
	 */
	public function getSelectionHint();
}