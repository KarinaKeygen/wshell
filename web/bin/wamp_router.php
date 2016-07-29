<?php

require '../vendor/autoload.php';

use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;

$router = new Router();
$router->addTransportProvider(new RatchetTransportProvider("172.25.0.101", 5554));
$router->start();
