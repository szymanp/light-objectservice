<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Resource\Origin;
use Light\ObjectAccess\Resource\ResolvedCollectionResource;
use Light\ObjectAccess\Transaction\Util\DummyTransaction;
use Light\ObjectAccess\Type\TypeRegistry;
use Light\ObjectAccess\Type\Util\DefaultTypeProvider;
use Light\ObjectService\Resource\Addressing\EndpointRelativeAddress;
use Light\ObjectService\Resource\Util\DefaultExecutionEnvironment;
use Szyman\ObjectService\Configuration\Endpoint;
use Light\ObjectService\Service\EndpointRegistry;
use Szyman\ObjectService\Configuration\Util\DefaultObjectProvider;
use Symfony\Component\HttpFoundation\Request;

class Setup
{
	/** @var DefaultTypeProvider */
	private $typeProvider;

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
		$this->typeProvider->addType($postType = new PostType($this->database));
		$this->typeProvider->addType($postCollectionType = new PostCollectionType($this->database));

		$this->objectProvider = new DefaultObjectProvider();
		$this->endpoint = Endpoint::create($endpointBase, $this->objectProvider, $this->typeProvider);

		$this->endpointRegistry = new EndpointRegistry();
		$this->endpointRegistry->addEndpoint($this->endpoint);

		$this->objectProvider->publishValue("resources/max", $this->database->getAuthor(1010));
		$this->objectProvider->publishCollection("collections/post", $postCollectionType);

		// Assign canonical base addresses to types
		$postType->setCanonicalBase($this->objectProvider->getResourceFactory("collections/post")->createResource($this->endpoint)->getAddress());
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
		return $this->endpoint->getTypeRegistry();
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
	 * @return DefaultExecutionEnvironment
	 */
	public function getExecutionParameters()
	{
		$ep = new DefaultExecutionEnvironment();
		$ep->setEndpointRegistry($this->endpointRegistry);
		$ep->setTransaction(new DummyTransaction());
		$ep->setEndpoint($this->getEndpoint());
		return $ep;
	}
}