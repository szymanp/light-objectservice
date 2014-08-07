<?php
namespace Light\ObjectService\Service;

class JsonClient
{
	/**
	 * @var \PestJSON
	 */
	private $pest;

	public function __construct($service)
	{
		if (isset($GLOBALS['test.url']))
		{
			$serviceUrl = $GLOBALS['test.url'] . $service;
			$this->pest = new \PestJSON($serviceUrl);
		}
	}
	
	/**
	 * @return boolean	true if the REST client is available.
	 */
	public function isAvailable()
	{
		return !is_null($this->pest);
	}

	/**
	 * @param string $url
	 * @return array
	 */
	public function get($url)
	{
		return $this->pest->get($url);
	}
	
	
}

