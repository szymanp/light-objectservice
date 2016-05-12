<?php
namespace Light\ObjectService\TestData;

use Light\ObjectAccess\Transaction\Transaction;
use Light\ObjectAccess\Type\Complex\Create;
use Light\ObjectAccess\Type\Util\CollectionResourceProperty;
use Light\ObjectAccess\Type\Util\DefaultComplexType;
use Light\ObjectAccess\Type\Util\DefaultProperty;
use Light\ObjectAccess\Resource\ResolvedObject;
use Light\ObjectAccess\Resource\Origin_Unavailable;
use Light\ObjectService\Resource\Selection\Selection;
use Szyman\ObjectService\Resource\Projection\FieldSelection;

class AuthorType extends DefaultComplexType implements Create, FieldSelection
{
	/** @var Database */
	private $database;

	public function __construct(Database $database)
	{
		parent::__construct(Author::class);
		$this->database = $database;
		$this->addProperty(new DefaultProperty("id", "int"));
		$this->addProperty(new DefaultProperty("name", "string"));
		$this->addProperty(new DefaultProperty("age", "int"));
		$this->addProperty(new CollectionResourceProperty("posts", Post::class . "[]"));
	}

	/**
	 * @inheritdoc
	 */
	public function createObject(Transaction $transaction)
	{
		// TODO Fix transaction
		return $this->database->createAuthor();
	}

    /** @inheritdoc */
    public function getDefaultSelection(ResolvedObject $object)
    {
        if ($object->getOrigin() instanceof Origin_Unavailable)
        {
            return Selection::create($object->getTypeHelper())->fields("*");
        }
        else
        {
            return Selection::create($object->getTypeHelper())->fields("*, -*C");
        }
    }

}