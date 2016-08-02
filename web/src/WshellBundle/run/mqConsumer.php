<?php

// TODO create as daemon

require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;

// settings server connection
$conn = new AMQPConnection('localhost', 5672, 'guest', 'guest');

// settings channel
$channel = $conn->channel();
$channelName = 'hello';
$channel->queue_declare($channelName, false, true, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";


// listen
$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
    /*
     *   run unit
     */
    sleep(3);
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
$channel->basic_qos(null, 1, null);
$channel->basic_consume($channelName, '', false, false, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}