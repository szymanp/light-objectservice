<?php
namespace Light\ObjectService\TestData;

class Database
{
	private $posts = array();
	private $authors = array();

	private $nextPostId = 5050;
	private $nextAuthorId = 2020;

	public function __construct()
	{
		$author = new Author(1010, "Max Ray");
		$author->setAge(35);
		$this->addAuthor($author);

		$author = new Author(1020, "Johnny Doe");
		$author->setAge(18);
		$this->addAuthor($author);

		$post = new Post(4040);
		$post->setAuthor($this->getAuthor(1010));
		$post->setTitle("First post");
		$post->setText("Lorem ipsum dolor");
		$this->addPost($post);

		$post = new Post(4041);
		$post->setAuthor($this->getAuthor(1010));
		$post->setTitle("Second post");
		$post->setText("Lorem lorem");
		$this->addPost($post);

		$post = new Post(4042);
		$post->setAuthor($this->getAuthor(1020));
		$post->setTitle("Is this working?");
		$post->setText("Let us test it");
		$this->addPost($post);
	}

	public function getPosts()
	{
		return $this->posts;
	}

	public function getAuthors()
	{
		return $this->authors;
	}

	public function getPostsForAuthor(Author $author)
	{
		$posts = array();
		foreach($this->posts as $post)
		{
			if ($post->getAuthor() === $author)
			{
				$posts[] = $post;
			}
		}
		return $posts;
	}

	public function addPost(Post $post)
	{
		$this->posts[$post->getId()] = $post;
	}

	public function addAuthor(Author $author)
	{
		$this->authors[$author->getId()] = $author;
	}

	public function removeAuthor(Author $author)
	{
		$key = array_search($author, $this->authors, true);
		if ($key !== false)
		{
			unset($this->authors[$key]);
		}
	}

	/**
	 * @param $id
	 * @return Author
	 */
	public function getAuthor($id)
	{
		return @ $this->authors[$id];
	}

	/**
	 * @param $id
	 * @return Post
	 */
	public function getPost($id)
	{
		return @ $this->posts[$id];
	}

	/**
	 * Returns an arbitrary Author object.
	 * @return Author
	 */
	public function getAnyAuthor()
	{
		return current($this->authors);
	}

	/**
	 * Returns an arbitrary Post object.
	 * @return Post
	 */
	public function getAnyPost()
	{
		return current($this->posts);
	}
	
	/**
	 * @return int
	 */
	public function getNextPostId()
	{
		return $this->nextPostId++;
	}

	/**
	 * Returns a new post.
	 * @return Post
	 */
	public function createPost()
	{
		$this->addPost($post = new Post($this->getNextPostId()));
		return $post;
	}
	
	/**
	 * Returns a new author.
	 * @return Author
	 */
	public function createAuthor()
	{
		$this->addAuthor($author = new Author($this->nextAuthorId++));
		return $author;
	}
}