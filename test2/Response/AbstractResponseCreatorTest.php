<?php
namespace Szyman\ObjectService\Response;

use Light\ObjectService\Resource\Projection\DataEntity;
use Szyman\ObjectService\Configuration\Util\TypeBasedResponseContentTypeMap;
use Szyman\ObjectService\Service\ResponseCreator;

abstract class AbstractResponseCreatorTest extends \PHPUnit_Framework_TestCase
{
	/** @var StandardErrorResponseCreatorTest_StructureSerializer */
	protected $structureSerializer;
	/** @var StandardErrorResponseCreatorTest_DataSerializer */
	protected $dataSerializer;
	/** @var TypeBasedResponseContentTypeMap */
	protected $map;
	/** @var ResponseCreator */
	protected $creator;

	protected function setUp()
	{
		$this->structureSerializer = new AbstractResponseCreatorTest_StructureSerializer;
		$this->dataSerializer = new AbstractResponseCreatorTest_DataSerializer;
		$this->map = new TypeBasedResponseContentTypeMap();
		$this->map->addClass(\Exception::class, "TEST", "application/vnd.exception+json");

		$this->creator = $this->newResponseCreator($this->structureSerializer, $this->dataSerializer, $this->map);
	}

	abstract protected function newResponseCreator($structureSer, $dataSer, $map);
}

class AbstractResponseCreatorTest_StructureSerializer implements StructureSerializer
{
	public $data;

	public function serializeStructure(DataEntity $dataEntity)
	{
		$this->data = $dataEntity;
		return "nothing";
	}
}

class AbstractResponseCreatorTest_DataSerializer implements DataSerializer
{
	public $data;

	public function serializeData($data)
	{
		$this->data = $data;
		return "dummy string";
	}

	public function getFormatName()
	{
		return "TEST";
	}
}