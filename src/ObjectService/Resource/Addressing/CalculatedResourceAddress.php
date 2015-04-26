<?php
namespace Light\ObjectService\Resource\Addressing;

use Light\ObjectAccess\Query\Scope;
use Light\ObjectAccess\Resource\Addressing\ResourceAddress;

/**
 * A resource address that can be computed at the moment of first access.
 *
 * This class is useful when the address of the resource can depend on some state that is not finalized
 * at the moment the resource is created.
 */
class CalculatedResourceAddress implements ResourceAddress
{
    /** @var \Closure */
    private $calculateFn;
    /** @var ResourceAddress */
    private $calculatedAddress;

    public function __construct(\Closure $calculateFn)
    {
        $this->calculateFn = $calculateFn;
    }

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
            $fn = $this->calculateFn;
            $this->calculatedAddress = $fn();
        }
    }
}