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

	/** @var Database */
	private $database;

	/**
	 * @return Setup
	 */
	public static function create()
	{
		return new self();
	}

	protected function __construct()
	{
		$this->database = new Database();

		$this->typeProvider = new DefaultTypeProvider();
		$this->typeProvider->addType(new AuthorType());
		$this->typeProvider->addType(new PostType());
		$this->typeProvider->addType(new PostCollectionType($this->database));

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
	 * @return TypeRegistry
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

	/**
	 * @return Database
	 */
	public function getDatabase()
	{
		return $this->database;
	}

}