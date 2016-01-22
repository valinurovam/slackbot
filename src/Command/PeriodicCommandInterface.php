<?php

namespace SlackBot\Command;

use Psr\Log\LoggerInterface;
use SlackBot\Response\ResponseInterface;

interface PeriodicCommandInterface
{
    /**
     * CommandInterface constructor.
     * @param int $interval
     * @param LoggerInterface $logger
     */
    public function __construct($interval, LoggerInterface $logger);

    /**
     * @return ResponseInterface
     */
    public function execute();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getInterval();
}
