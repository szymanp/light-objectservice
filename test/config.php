<?php

$BASE_PATH = realpath(dirname(__FILE__));

// Set the working directory to the root of the tests
chdir($BASE_PATH);

require_once "../vendor/autoload.php";
require_once "ObjectService/MockupModel.php";
require_once "ObjectService/Service/MockupResponse.php";
@include_once "config-local.php";

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

