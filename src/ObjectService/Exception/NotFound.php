<?php
namespace Light\ObjectService\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * The requested resource was not found on the sever.
 */
class NotFound extends \Exception implements HttpExceptionInformation
{
	/** @var string */
	private $url;

	/**
	 * @param string $url		The URL that was requested but not found.
	 * @param string $message
	 */
	public function __construct($url, $message = "", $code = 0, \Exception $previous = null)
	{
		$this->url = $url;
		parent::__construct($message, $code, $previous);
	}

	/**
	 * Returns the URL that was requested but not found.
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	public function getHttpStatusCode()
	{
		return Response::HTTP_NOT_FOUND;
	}
}