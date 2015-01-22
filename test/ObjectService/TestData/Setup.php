<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Transaction\Util\DummyTransaction;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Resource\Util\DefaultExecutionParameters;
use Light\ObjectService\Service\Endpoint;
use Light\ObjectService\Service\EndpointRegistry;
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

	/** @var EndpointRegistry */
	private $endpointRegistry;

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
		$this->typeProvider->addType(new PostType($this->database));
		$this->typeProvider->addType(new PostCollectionType($this->database));

		$this->typeRegistry = new TypeRegistry($this->typeProvider);
		$this->objectProvider = new DefaultObjectProvider($this->typeRegistry);
		$this->endpoint = Endpoint::create("http://example.org/", $this->objectProvider);

		$this->endpointRegistry = new EndpointRegistry();
		$this->endpointRegistry->addEndpoint($this->endpoint);

		$this->objectProvider->publishValue("resources/max", $this->database->getAuthor(1010));
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

	/**
	 * Returns a new ExecutionParameters object.
	 * @return DefaultExecutionParameters
	 */
	public function getExecutionParameters()
	{
		$ep = new DefaultExecutionParameters();
		$ep->setEndpointRegistry($this->endpointRegistry);
		$ep->setTransaction(new DummyTransaction());
		return $ep;
	}
}