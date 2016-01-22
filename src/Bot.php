<?php

namespace SlackBot;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use Slack\ChannelInterface;
use Slack\Message\Message;
use Slack\Payload;
use Slack\RealTimeClient;
use Slack\User;
use SlackBot\Invoker\Invoker;

class Bot
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Invoker
     */
    protected $invoker;

    /**
     * @var RealTimeClient
     */
    protected $client;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var User
     */
    protected $botUser;

    public function __construct(
        LoopInterface $loop,
        RealTimeClient $client,
        Invoker $invoker,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->invoker = $invoker;
        $this->client = $client;
        $this->loop = $loop;
    }

    /**
     * Run main bot process
     */
    public function run()
    {
        $this->prepareOnMessage();
        $this->preparePeriodicCommands();
        $this->connect();

        $this->loop->run();
    }

    /**
     * @param string $message
     * @param string $destinationId
     */
    protected function sendMessageByDestId($message, $destinationId)
    {
        $this->client
            ->getChannelGroupOrDMByID($destinationId)
            ->then(
                function (ChannelInterface $channel) use ($message) {
                    $this->client->send($message, $channel);
                }
            );
    }

    protected function sendMessageByDestName($message, $destinationName)
    {
        if ($destinationName[0] === '#') {
            $destinationName = substr($destinationName, 1);
            $this->client
                ->getChannelByName($destinationName)
                ->then(
                    function (ChannelInterface $channel) use ($message) {
                        $this->client->send($message, $channel);
                    }
                );
        }

        if ($destinationName[0] === '@') {
            $destinationName = substr($destinationName, 1);
            $this->client->getUserByName($destinationName)
                ->then(
                    function (User $user) use ($message) {
                        $this->client->getDMByUser($user)
                            ->then(
                                function (ChannelInterface $channel) use ($message) {
                                    $this->client->send($message, $channel);
                                }
                            );
                    }
                );
        }
    }

    /**
     * Prepare onMessage handler
     */
    protected function prepareOnMessage()
    {
        $this->client
            ->on(
                'message',
                function (Payload $data) {
                    /**
                     * Skip self messages
                     */
                    if ($data['user'] === $this->botUser->getId()) {
                        return;
                    }

                    $this->logger->info(
                        sprintf(
                            "Message received: %s",
                            json_encode($data->getData())
                        )
                    );
                    $message = new Message($this->client, $data->getData());
                    $invokeResult = $this->invoker->execute($message);

                    $this->sendMessageByDestId($invokeResult, $data['channel']);

                    $this->logger->info(
                        sprintf(
                            "Response sent: %s",
                            $invokeResult
                        )
                    );
                }
            );
    }

    /**
     * Connect to slack server
     */
    protected function connect()
    {
        $this
            ->client
            ->connect()
            ->then(
                function () {
                    return $this->client->getAuthedUser();
                }
            )
            ->then(
                function (User $user) {
                    $this->botUser = $user;
                    $this->logger->info("Connected as " . $this->botUser->getUsername());
                }
            );
    }

    /**
     * Prepare periodic commands
     */
    protected function preparePeriodicCommands()
    {
        $commands = $this->invoker->getPeriodicCommands();

        foreach ($commands as $command) {
            $this->loop->addPeriodicTimer(
                $command->getInterval(),
                function () use ($command) {
                    $cmdResult = $command->execute();
                    $this->sendMessageByDestName(
                        $cmdResult->getText(),
                        $cmdResult->getDestination()
                    );
                }
            );
        }
    }
}
