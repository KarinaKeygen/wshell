<?php

use Ratchet\Server\IoServer;
use Wshell\Bridges\Chat;

require('../../vendor/autoload.php');

$server = IoServer::factory(
    new Chat(),
    8080
);

$server->run();