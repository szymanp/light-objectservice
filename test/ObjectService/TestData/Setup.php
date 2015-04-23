<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Transaction\Util\DummyTransaction;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Util\DefaultExecutionParameters;
use Light\ObjectService\Service\Endpoint;
use Light\ObjectService\Service\EndpointRegistry;
use Light\ObjectService\Service\Util\DefaultObjectProvider;
use Symfony\Component\HttpFoundation\Request;

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

	/**
	 * @return Setup
	 */
	public static function createWithCurrentUrl()
	{
		$httpRequest = Request::createFromGlobals();
		return new self($httpRequest->getSchemeAndHttpHost() . $httpRequest->getBaseUrl() . "/");
	}

	protected function __construct($endpointBase = "http://example.org/")
	{
		$this->database = new Database();

		$this->typeProvider = new DefaultTypeProvider();
		$this->typeProvider->addType(new AuthorType($this->database));
		$this->typeProvider->addType(new PostType($this->database));
		$this->typeProvider->addType($postCollectionType = new PostCollectionType($this->database));

		$this->typeRegistry = new TypeRegistry($this->typeProvider);
		$this->objectProvider = new DefaultObjectProvider($this->typeRegistry);
		$this->endpoint = Endpoint::create($endpointBase, $this->objectProvider);

		$this->endpointRegistry = new EndpointRegistry();
		$this->endpointRegistry->addEndpoint($this->endpoint);

		$this->objectProvider->publishValue("resources/max", $this->database->getAuthor(1010));
		$this->objectProvider->publishCollection("collections/post", $postCollectionType);
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
	 * @return EndpointRegistry
	 */
	public function getEndpointRegistry()
	{
		return $this->endpointRegistry;
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
		$ep->setEndpoint($this->getEndpoint());
		return $ep;
	}
}