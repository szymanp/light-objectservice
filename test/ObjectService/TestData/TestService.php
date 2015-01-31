<?php
namespace Light\ObjectService\TestData;

use Light\ObjectService\Service\EndpointContainer;

chdir(__DIR__ . "/../../..");
require_once 'test/config.php';

$setup = Setup::create();

$container = new EndpointContainer($setup->getEndpointRegistry());
$container->run();