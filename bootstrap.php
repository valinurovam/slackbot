<?php

include 'vendor/autoload.php';

date_default_timezone_set('Europe/Moscow');

$logger = new \SlackBot\Logger\EchoLogger();

$invoker = new \SlackBot\Invoker();
$invoker->addCommand(
    new \SlackBot\Command\PingPong($logger)
);

$loop = React\EventLoop\Factory::create();

$client = new Slack\RealTimeClient($loop);
$client->setToken('SLACK-TOKEN');

$bot = new \SlackBot\Bot($loop, $client, $invoker, $logger);
$bot->run();
