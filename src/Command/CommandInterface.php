<?php

namespace SlackBot\Command;

interface CommandInterface
{
    /**
     * CommandInterface constructor.
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger);

    /**
     * @param \Slack\Message\Message $message
     * @return string
     */
    public function execute(\Slack\Message\Message $message);

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
