<?php
namespace Light\ObjectService\TestData;

class JsonClient
{
	/** @var \PestJSON */
	private $pest;

	public function __construct($service = "ObjectService/TestData/TestService.php")
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
	 * Skips a PHPUnit test if the client is not configured.
	 */
	public function skipTestIfNotConfigured()
	{
		if (!$this->isAvailable())
		{
			\PHPUnit_Framework_TestCase::markTestSkipped("Missing test.url setup in phpunit.xml");
		}
	}

	/**
	 * @param string $url
	 * @return array
	 */
	public function get($url)
	{
		return $this->pest->get($url);
	}

	public function put($url, array $data)
	{
		return $this->pest->put($url, $data);
	}
}