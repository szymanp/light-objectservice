<?php
namespace Szyman\ObjectService\Configuration\Util;

use Light\ObjectAccess\Type\ComplexType;
use Light\ObjectService\TestData\AuthorType;
use Light\ObjectService\TestData\Database;

class PluggableRequestBodyDeserializerFactoryTest extends \PHPUnit_Framework_TestCase
{
	/** @var PluggableRequestBodyDeserializerFactory */
	private $factory;

	public function setUp()
	{
		parent::setUp();
		$this->factory = new PluggableRequestBodyDeserializerFactory();
	}

	/**
	 * @expectedException	Szyman\Exception\UnexpectedValueException
	 */
	public function testDeserializerInvalidReturnValue()
	{
		$this->factory->registerDeserializer(
			PluggableRequestBodyDeserializerFactory::COMPLEX_VALUE_REPRESENTATION,
			"application/json",
			function(ComplexType $type)
			{
				return "hello world";
			});

		// This should throw an exception as the closure above returns "hello world" instead of a deserializer.
		$this->factory->newComplexValueRepresentationDeserializer("application/json", new AuthorType(new Database()));
	}
}
