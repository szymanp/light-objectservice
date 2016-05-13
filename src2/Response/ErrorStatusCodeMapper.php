<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectAccess\Exception\InvalidActionException;
use Light\ObjectAccess\Exception\PropertyException;
use Light\ObjectAccess\Exception\TypeCapabilityException;
use Szyman\Exception\InvalidArgumentTypeException;
use Szyman\Exception\InvalidArgumentValueException;

/**
 * Maps arbitrary exceptions to HTTP status codes.
 */
final class ErrorStatusCodeMapper
{
    private $map = array();

    /**
     * @return ErrorStatusCodeMapper
     */
    public static function newEmptyMapper()
    {
        return new self;
    }
    
    /**
     * @return ErrorStatusCodeMapper
     */
    public static function newStandardMapper()
    {
        $mapper = new self;
        
        $mapper
            ->addException(InvalidActionException::class, 400)
            ->addException(PropertyException::class, 400)
            ->addException(TypeCapabilityException::class, 400)
            ->addException(\RuntimeException::class, 500);
        
        return $mapper;
    }

    private function __construct()
    {
        // Private constructor - use one of the static factories.
    }

    /**
     * Maps an exception class to a HTTP status code.
     * @param string    $class  A class name for an exception.
     * @param integer   $code   The HTTP status code for this exception.
     * @return $this
     */    
    public function addException($class, $code)
    {
        if (!is_subclass_of($class, 'Exception'))
        {
            throw new InvalidArgumentValueException('$class', $class, 'A subclass of Exception');
        }
        if (!is_integer($code))
        {
            throw new InvalidArgumentTypeException('$code', $code, 'integer');
        }
        
        $this->map[$class] = $code;
        return $this;
    }
    
    /**
     * Returns the status code for the given exception.
     * @param \Exception    $ex
     * @return integer  A HTTP status code. If no exception matches, then this method returns the code 500.
     */
    public function getStatusCode(\Exception $ex)
    {
        // Find the innermost exception.
        while(true)
        {
            $innerex = $ex->getPrevious();
            if (is_null($innerex))
            {
                break;
            }
            else
            {
                $ex = $innerex;
            }
        }
        
        // Find the code for that exception.
        foreach($this->map as $class => $code)
        {
            if ($ex instanceof $class)
            {
                return $code;
            }
        }
        
        return 500;
    }
}
