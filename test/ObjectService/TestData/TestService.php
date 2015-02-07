<?php
namespace Light\ObjectService\TestData;

use Light\ObjectService\Protocol\SimpleGetProtocol;
use Light\ObjectService\Service\EndpointContainer;

chdir(__DIR__ . "/../../..");
require_once 'test/config.php';

$setup = Setup::createWithCurrentUrl();

$container = new EndpointContainer($setup->getEndpointRegistry());
$container->addProtocol(new SimpleGetProtocol());
$container->setProduction(false);
$container->run();