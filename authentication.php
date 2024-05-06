<?php

require_once 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test', 'testHost');
$channel = $connection->channel();

// Declare the first queue
$channel->queue_declare('successful_logins', false, true, false, false);

// Declare the second queue
$channel->queue_declare('failed_logins', false, true, false, false);

echo " Awaiting Login Traffic\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

// Consume messages from the first queue
$channel->basic_consume('successful_logins', '', false, true, false, false, $callback);

// Consume messages from the second queue
$channel->basic_consume('failed_logins', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
