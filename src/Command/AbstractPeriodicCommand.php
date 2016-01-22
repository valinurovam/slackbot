<?php

namespace SlackBot\Command;

use Psr\Log\LoggerInterface;

abstract class AbstractPeriodicCommand implements PeriodicCommandInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $interval;

    /**
     * AbstractPeriodicCommand constructor.
     * @param int $interval
     * @param LoggerInterface $logger
     */
    public function __construct($interval, LoggerInterface $logger)
    {
        $this->interval = $interval;
        $this->logger = $logger;
    }

    /**
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }
}
