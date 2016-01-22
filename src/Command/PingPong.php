<?php

namespace SlackBot\Command;

use Slack\Message\Message;

class PingPong extends AbstractCommand
{

    /**
     * @inheritdoc
     */
    public function execute(Message $message)
    {
        return 'pong';
    }

    /**
     * @inheritdoc
     */
    public function getHelp()
    {
        return 'Ping-pong command';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ping';
    }
}
