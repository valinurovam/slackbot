<?php

namespace SlackBot\Command;

use Psr\Log\LoggerInterface;
use Slack\Message\Message;

interface CommandInterface
{
    /**
     * CommandInterface constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger);

    /**
     * @param Message $message
     * @return string
     */
    public function execute(Message $message);

    /**
     * Return well formatted help message for clients
     *
     * @return mixed
     */
    public function getHelp();

    /**
     * @return string
     */
    public function getName();
}
