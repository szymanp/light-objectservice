<?php
namespace Light\ObjectService\Service;

use Light\ObjectService\Service\Util\InvocationParametersObject;
use Light\ObjectService\ObjectRegistry;
use Light\ObjectService\Service\Invocation;
use Light\ObjectService\Service\Json\JsonRequestReader;
use Light\Util\HTTP\Request as HTTPRequest;
use Light\Util\HTTP\Response as HTTPResponse;
use Light\ObjectService\Service\Json\JsonResponse;
use Light\ObjectService\Mockup\PostModel;
use Light\ObjectService\Mockup\PostCollectionModel;
use Light\ObjectService\Mockup\Comment;
use Light\ObjectService\Mockup\TypeFactory;
use Light\ObjectService\Mockup\CommentCollectionType;
use Light\ObjectService\Mockup\Database;

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

$httpRequest = new HTTPRequest();
$httpResponse = new HTTPResponse();

$basePath = $_SERVER['SCRIPT_NAME'] . "/";

$jsonRequestReader = new JsonRequestReader($basePath);
$jsonRequest = $jsonRequestReader->read($httpRequest);
$jsonResponse = new JsonResponse($httpResponse);

$invocation = new Invocation($params, $jsonRequest, $jsonResponse);
$invocation->invoke();

// Save database
Database::save($dbfile);