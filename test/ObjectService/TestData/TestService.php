<?php
namespace Light\ObjectService\TestData;

use Light\ObjectService\Service\EndpointContainer;
use Light\ObjectService\Service\Util\DefaultGetRequestReader;

chdir(__DIR__ . "/../../..");
require_once 'test/config.php';

$setup = Setup::createWithCurrentUrl();

$container = new EndpointContainer($setup->getEndpointRegistry());
$container->setPrimaryRequestReader(new DefaultGetRequestReader());
$container->run();