<?php
namespace Light\ObjectService\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnsupportedMediaType extends \Exception implements HttpExceptionInformation
{
	/**
	 * @param string $mediaType	The media type that was requested.
	 * @param string $message	An optional message explaining the context for the use of this the media type.
	 */
	public function __construct($mediaType = "", $message = "")
	{
		parent::__construct($mediaType . (empty($message) ? "" : " - " . $message));
	}

	public function getHttpStatusCode()
	{
		return Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
	}
}