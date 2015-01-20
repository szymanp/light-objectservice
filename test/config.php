<?php

$BASE_PATH = realpath(dirname(__FILE__));

require_once "vendor/autoload.php";

// Include test data from ObjectAccess
chdir("vendor/light/objectaccess");
include_once "test/ObjectAccess/TestData/Setup.php";

// Set the working directory to the root of the tests
chdir($BASE_PATH);
include_once "ObjectService/TestData/Setup.php";
@include_once "config-local.php";

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
