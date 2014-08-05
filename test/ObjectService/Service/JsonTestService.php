<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Service\Util\InvocationParametersObject;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Service\Invocation;
use Light\ObjectService\Service\Json\JsonRequestReader;
use Light\Util\HTTP\Response as HTTPResponse;
use Light\ObjectService\Service\Json\JsonResponse;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\Comment;
use Light\ObjectService\Mockup\TypeFactory;
use Light\ObjectService\Mockup\CommentCollectionType;
use Light\ObjectService\Mockup\Database;
use Light\ObjectService\Service;

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../MockupModel.php';

// Load database
Database::load($dbfile = "../database.serialize");

// Setup published objects and types
$registry = new ObjectRegistry();
$registry->addType(new CommentCollectionType());
$registry->publishCollection("post", new PostCollectionModel());

$registry->getNameRegistry()->addTypeBaseUri("Light\\ObjectService\\Mockup", "//mockup");

// Configure the service
$params = new InvocationParametersObject();
$params->setObjectRegistry($registry);

$basePath = $_SERVER['SCRIPT_NAME'] . "/";

$jsonRequestReader = new JsonRequestReader($basePath);
$jsonResponse = new JsonResponse(new HTTPResponse());

$service = new Service($params);
$service->addRequestReader($jsonRequestReader);
$service->addResponse($jsonResponse);
$service->invoke();

// Save database
Database::save($dbfile);