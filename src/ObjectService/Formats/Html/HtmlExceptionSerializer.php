<?php
namespace Light\ObjectService\Formats\Html;

use Light\ObjectService\Service\EndpointContainer_Exception;
use Light\ObjectService\Service\Protocol\ExceptionSerializer;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serializes exception to a user-readable format in HTML.
 */
class HtmlExceptionSerializer implements ExceptionSerializer
{
	/**
	 * Serializes an exception.
	 * @param \Exception $e
	 * @return string
	 */
	public function serialize(\Exception $e)
	{
		$result = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "HtmlExceptionTemplate.html");

		if ($e instanceof EndpointContainer_Exception)
		{
			$title = Response::$statusTexts[$e->getCode()];
		}
		else
		{
			$title = "Container Error";
		}

		$values = array();
		$values['{{title}}']   = $title;
		$values['{{message}}'] = $e->getMessage();
		$values['{{trace}}'] = $this->getStackTrace($e);

		$result = str_replace(array_keys($values), array_values($values), $result);

		return $result;
	}

	/**
	 * Returns the content type produced by this serializer.
	 * @return string
	 */
	public function getContentType()
	{
		return "text/html";
	}

	private function getStackTrace(\Exception $e)
	{
		$trace = "";
		$currentException = $e;
		while ($currentException)
		{
			$trace .= get_class($currentException) . ": " . $currentException->getMessage() . "\n";
			$trace .= $currentException->getTraceAsString();
			if ($currentException = $currentException->getPrevious())
			{
				$trace .= "\n\nCaused by:\n";
			}
		}
		return $trace;
	}
}