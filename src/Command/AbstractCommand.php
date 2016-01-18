<?php

namespace SlackBot\Command;

use Psr\Log\LoggerInterface;
use Slack\Message\Message;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CommandInterface constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    abstract public function execute(Message $message);

    /**
     * @inheritdoc
     */
    abstract public function getHelp();

    /**
     * @inheritdoc
     */
    abstract public function getName();
}
