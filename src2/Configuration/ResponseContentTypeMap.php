<?php
namespace Szyman\ObjectService\Configuration;

use Light\ObjectAccess\Resource\ResolvedResource;

interface ResponseContentTypeMap
{
	// TODO
	public function getContentType(ResolvedResource $resource, $dataSerializerClass);
}