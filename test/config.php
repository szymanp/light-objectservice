<?php

$BASE_PATH = realpath(dirname(__FILE__));

require_once "vendor/autoload.php";

// Include test data from ObjectAccess
chdir("vendor/light/objectaccess");
include_once "test/ObjectAccess/TestData/Setup.php";

// Set the working directory to the root of the tests
chdir($BASE_PATH);
include_once "ObjectService/TestData/Post.php";
include_once "ObjectService/TestData/PostType.php";
include_once "ObjectService/TestData/Author.php";
include_once "ObjectService/TestData/AuthorType.php";
include_once "ObjectService/TestData/PostCollectionType.php";
include_once "ObjectService/TestData/Database.php";
include_once "ObjectService/TestData/Setup.php";
include_once "ObjectService/TestData/RemoteJsonClient.php";
@include_once "config-local.php";

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
