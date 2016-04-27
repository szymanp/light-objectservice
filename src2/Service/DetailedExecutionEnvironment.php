<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Transaction\Transaction;
use Szyman\ObjectService\Configuration\Configuration;
use Symfony\Component\HttpFoundation\Request;

/**
 * An execution environment providing detailed information about the processed request.
 *
 * This environment is used by the <kbd>RequestProcessor</kbd>.
 */
final class DetailedExecutionEnvironment implements ExecutionEnvironment
{
	/** @var Configuration */
	private $conf;
	/** @var Transaction */
	private $tx;
	/** @var RequestComponents */
	private $rc;
	/** @var Request */
	private $request;

	public function __construct(Configuration $conf, Transaction $tx, Request $request, RequestComponents $rc)
	{
		$this->conf = $conf;
		$this->tx = $tx;
		$this->rc = $rc;
		$this->request = $request;
	}

	/** @inheritdoc */
	public function getTransaction()
	{
		return $this->tx;
	}
	
	/** @inheritdoc */
	public function getEndpointRegistry()
	{
		return $this->conf->getEndpointRegistry();
	}

	/** @inheritdoc */
	public function getEndpoint()
	{
		return $this->rc->getEndpointAddress()->getEndpoint();
	}
    
    /** @inheritdoc */
    public function getLogger()
    {
        return $this->conf->getLogger();
    }
	
	/**
	 * Returns the Configuration of the <kbd>RequestProcessor</kbd>
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->conf;
	}
	
	/**
	 * Returns the decoded request.
	 * @return RequestComponents
	 */
	public function getRequestComponents()
	{
		return $this->rc;
	}
	
	/**
	 * Returns the HTTP request that is being processed.
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}
}
