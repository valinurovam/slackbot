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
}
