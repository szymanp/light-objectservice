<?php
namespace Light\ObjectService\TestData;

class Author
{
	private $id;
	private $name;
	private $age;

	public function __construct($id, $name = null)
	{
		$this->id = $id;
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return null
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param null $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getAge()
	{
		return $this->age;
	}

	/**
	 * @param mixed $age
	 */
	public function setAge($age)
	{
		$this->age = $age;
	}


}