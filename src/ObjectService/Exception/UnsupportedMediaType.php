<?php
namespace Light\ObjectService\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnsupportedMediaType extends \Exception implements HttpExceptionInformation
{
	/**
	 * @param string $mediaType	The media type that was requested.
	 */
	public function __construct($mediaType = "")
	{
		parent::__construct($mediaType);
	}

	public function getHttpStatusCode()
	{
		return Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
	}
}