<?php

require '../vendor/autoload.php';

use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;

$router = new Router();
$router->addTransportProvider(new RatchetTransportProvider("172.24.0.102", 5550));
$router->start();
