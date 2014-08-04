<?php
namespace Light\ObjectService\Service\Response;

interface Response
{
	function sendEntity(DataEntity $entity);
	
	function sendNewEntity($resourcePath, DataEntity $entity);
	
	function sendNotFound();
	
	function sendBadRequest();
	
	function sendInternalError(\Exception $e);
}
