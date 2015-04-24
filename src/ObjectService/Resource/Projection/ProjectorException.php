<?php
namespace Light\ObjectService\Resource\Projection;

/**
 * This exception carries the path information context for exceptions caught during projection.
 */
class ProjectorException extends \Exception
{
	public function __construct(array $path, \Exception $previous)
	{
		$message = $previous->getMessage();
		if (empty($message))
		{
			$message = get_class($previous);
		}
		$message .= " at path " . implode('/', $path);
		parent::__construct($message, 0, $previous);
	}
}