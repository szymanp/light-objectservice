<?php
namespace Light\ObjectService\TestData;

class Post
{
	private $id;
	/** @var Author */
	private $author;
	private $title;
	private $text;
	
	public function __construct($id = null)
	{
		$this->id = $id;
	}
	
	public function setId($id)
	{
		if (is_null($this->id))
		{
			$this->id = $id;
		}
		else
		{
			throw new \Exception("Post already has ID=" . $this->id . ", cannot set new one ($id)");
		}
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return Author
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param Author $author
	 */
	public function setAuthor(Author $author)
	{
		$this->author = $author;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @param mixed $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}
}
