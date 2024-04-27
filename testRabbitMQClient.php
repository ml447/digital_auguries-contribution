#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

// Concatenate all command line arguments to form the message
$msg = implode(" ", array_slice($argv, 1));

// If no arguments are passed, set a default test message
if (empty($msg)) {
  $msg = "test message";
}

$request = array();
$request['type'] = "Login";
$request['username'] = "ml447";
$request['password'] = "ml447";
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

