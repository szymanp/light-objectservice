<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Resource\Addressing\ResourceAddress;

/**
 * A resource address that is computed on access.
 *
 * This class is useful when the address of the resource can depend on some state that is not finalized
 * at the moment the resource is created.
 */
abstract class CalculatedResourceAddress implements ResourceAddress
{
    /** @var \Closure */
    protected $calculateFn;

	/**
	 * Returns a resource address that is computed at the moment of first access.
	 * 
	 * The closure passed as an argument is called only once when one of the appender
	 * or getter methods is called on the address object. The state of the object is finalized
	 * at that moment.
	 *
	 * @param \Closure  $calculateFn	A closure returning a <kbd>ResourceAddress</kbd> object.
	 * @return CalculatedResourceAddress
	 */
	public static function staticAddress(\Closure $calculateFn)
	{
		return new CalculatedResourceAddress_Static($calculateFn);
	}
	
	/**
	 * Returns a resource address that is computed every time it is accessed.
	 * 
	 * The closure passed as an argument is called every time one of the appender
	 * or getter methods is called on the address object. Therefore the address
	 * always dynamically depends on the return value of the closure.
	 *
	 * @param \Closure  $calculateFn	A closure returning a <kbd>ResourceAddress</kbd> object.
	 * @return CalculatedResourceAddress
	 */
	public static function dynamicAddress(\Closure $calculateFn)
	{
		return new CalculatedResourceAddress_Dynamic($calculateFn);
	}

    protected function __construct(\Closure $calculateFn)
    {
        $this->calculateFn = $calculateFn;
    }
	
	/**
	 * @return ResourceAddress
	 */
	protected static function address(\Closure $fn)
	{
		$address = $fn();
		if (!($address instanceof ResourceAddress))
		{
			throw new \UnexpectedValueException(
				'Closure used in ' . CalculatedResourceAddress::class . ' did not return a ResourceAddress');
		}
		return $address;
	}
}

final class CalculatedResourceAddress_Static extends CalculatedResourceAddress
{
    /** @var ResourceAddress */
    private $calculatedAddress;

    /**
     * Returns a new address with the given scope appended at the end.
     * @param Scope $scope
     * @return ResourceAddress    A new ResourceAddress object representing the original address
     *                          with the scope object appended at the end.
     */
    public function appendScope(Scope $scope)
    {
        $this->materialize();
        return $this->calculatedAddress->appendScope($scope);
    }

    /**
     * Returns a new address with the given element appended at the end.
     * @param string $pathElement
     * @return ResourceAddress    A new ResourceAddress object representing the original address
     *                            with the new element appended at the end.
     */
    public function appendElement($pathElement)
    {
        $this->materialize();
        return $this->calculatedAddress->appendElement($pathElement);
    }

    /**
     * Returns true if the address has a string representation.
     * @return boolean
     */
    public function hasStringForm()
    {
        $this->materialize();
        return $this->calculatedAddress->hasStringForm();
    }

    /**
     * Returns the string representation of this address.
     * @return string    An address string, if it is available; otherwise, NULL.
     */
    public function getAsString()
    {
        $this->materialize();
        return $this->calculatedAddress->getAsString();
    }

    protected function materialize()
    {
        if (is_null($this->calculatedAddress))
        {
            $this->calculatedAddress = self::address($this->calculateFn);
        }
    }

}

final class CalculatedResourceAddress_Dynamic extends CalculatedResourceAddress
{
    /**
     * Returns a new address with the given scope appended at the end.
     * @param Scope $scope
     * @return ResourceAddress    A new ResourceAddress object representing the original address
     *                          with the scope object appended at the end.
     */
    public function appendScope(Scope $scope)
    {
		return $this->appendElement($scope);
    }

    /**
     * Returns a new address with the given element appended at the end.
     * @param string $pathElement
     * @return ResourceAddress    A new ResourceAddress object representing the original address
     *                            with the new element appended at the end.
     */
    public function appendElement($pathElement)
    {
		$originalFn = $this->calculateFn;
		return new self(function() use ($originalFn, $pathElement)
		{
			return CalculatedResourceAddress::address($originalFn)->appendElement($pathElement);
		});
    }

    /**
     * Returns true if the address has a string representation.
     * @return boolean
     */
    public function hasStringForm()
    {
        return $this->materialize()->hasStringForm();
    }

    /**
     * Returns the string representation of this address.
     * @return string    An address string, if it is available; otherwise, NULL.
     */
    public function getAsString()
    {
        return $this->materialize()->getAsString();
    }

    protected function materialize()
    {
		return self::address($this->calculateFn);
    }
}
