<?php

namespace SlackBot;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use Slack\ChannelInterface;
use Slack\Message\Message;
use Slack\Payload;
use Slack\RealTimeClient;
use Slack\User;

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

                    $this->sendMessage($invokeResult, $data['channel']);

                    $this->logger->info(
                        sprintf(
                            "Response sent: %s",
                            $invokeResult
                        )
                    );
                }
            );

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

        $this->loop->run();
    }

    /**
     * @param string $message
     * @param string $channelId
     */
    protected function sendMessage($message, $channelId)
    {
        $this->client
            ->getChannelGroupOrDMByID($channelId)
            ->then(
                function (ChannelInterface $channel) use ($message) {
                    $this->client->send($message, $channel);
                }
            );
    }
}
