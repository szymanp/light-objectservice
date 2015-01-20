<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Service\Endpoint;
use Light\ObjectService\Service\Util\DefaultObjectProvider;

class Setup
{
	/** @var DefaultTypeProvider */
	private $typeProvider;

	/** @var TypeRegistry */
	private $typeRegistry;

	/** @var DefaultObjectProvider */
	private $objectProvider;

	/** @var Endpoint */
	private $endpoint;

	/**
	 * @return Setup
	 */
	public static function create()
	{
		return new self();
	}

	protected function __construct()
	{
		$this->typeProvider = new DefaultTypeProvider();
		$this->typeProvider->addType(new AuthorType());

		$this->typeRegistry = new TypeRegistry($this->typeProvider);
		$this->objectProvider = new DefaultObjectProvider($this->typeRegistry);
		$this->endpoint = Endpoint::create("http://example.org/", $this->objectProvider);
	}

	/**
	 * @return Endpoint
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	/**
	 * @return DefaultObjectProvider
	 */
	public function getObjectProvider()
	{
		return $this->objectProvider;
	}

	/**
	 * @return mixed
	 */
	public function getTypeRegistry()
	{
		return $this->typeRegistry;
	}

	/**
	 * @return DefaultTypeProvider
	 */
	public function getTypeProvider()
	{
		return $this->typeProvider;
	}

}