<?php
namespace Light\ObjectService\Service\Protocol;

final class AcceptResult
{
	/** @var boolean */
	private $isAccepted = false;
	/** @var boolean */
	private $isBadMethod = false;
	/** @var boolean */
	private $isBadSuppliedContentType = false;
	/** @var boolean */
	private $isBadRequestedContentType = false;

	private $supportedMethods;
	private $supportedContentTypes;

	/**
	 * The protocol is capable of processing this request.
	 * @return AcceptResult
	 */
	public static function accepted()
	{
		$result = new self();
		$result->isAccepted = true;
		return $result;
	}

	/**
	 * The protocol does not support the method specified in the request.
	 * @param array $supportedMethods
	 * @return AcceptResult
	 */
	public static function badMethod(array $supportedMethods = array())
	{
		$result = new self();
		$result->isBadMethod = true;
		$result->supportedMethods = $supportedMethods;
		return $result;
	}

	/**
	 * The protocol does not support the content-type of the request entity.
	 * @param array $supportedContentTypes
	 * @return AcceptResult
	 */
	public static function badSuppliedContentType(array $supportedContentTypes = array())
	{
		$result = new self();
		$result->isBadSuppliedContentType = true;
		$result->supportedContentTypes = $supportedContentTypes;
		return $result;
	}

	/**
	 * The protocol does not support any of the response content-types accepted by the client.
	 * @param array $supportedContentTypes
	 * @return AcceptResult
	 */
	public static function badRequestedContentType(array $supportedContentTypes = array())
	{
		$result = new self();
		$result->isBadRequestedContentType = true;
		$result->supportedContentTypes = $supportedContentTypes;
		return $result;
	}

	protected function __construct()
	{
	}

	/**
	 * Returns true if the request was accepted.
	 * @return boolean
	 */
	public function isAccepted()
	{
		return $this->isAccepted;
	}

	/**
	 * @return boolean
	 */
	public function isBadMethod()
	{
		return $this->isBadMethod;
	}

	/**
	 * @return boolean
	 */
	public function isBadSuppliedContentType()
	{
		return $this->isBadSuppliedContentType;
	}

	/**
	 * @return boolean
	 */
	public function isBadRequestedContentType()
	{
		return $this->isBadRequestedContentType;
	}

	/**
	 * @return string[]
	 */
	public function getSupportedContentTypes()
	{
		return $this->supportedContentTypes;
	}

	/**
	 * @return string[]
	 */
	public function getSupportedMethods()
	{
		return $this->supportedMethods;
	}
}

