<?php

require __DIR__ . '/../vendor/autoload.php';

use Thruway\ClientSession;
use UnitBundle\Service\WampClient;
use Thruway\Transport\PawlTransportProvider;

$client = new WampClient("realm1", "ws://172.25.0.101:5554/");

$client->on('open', function (ClientSession $session) use ($client) {

    $session->subscribe('wshell.execute.result', function ($args) {
        var_dump($args);
        die();
    });

    $sessionId = $client->session->getSessionId();
    $message = [
      'units' => [
        [
        "index" => 1,
        "order" => 1,
        "require" => ['php'],
        'source' => 'echo 123*321;'
        ]
      ],
      'clientId' => (string)$sessionId
    ];

    $session->publish('wshell.execute.main', [json_encode($message)], [], ["acknowledge" => true])->then(
        function () {
            echo "Publish Acknowledged!\n";
        },
        function ($error) {
            // publish failed
            echo "Publish Error {$error}\n";
        }
    );
});

$client->start();
