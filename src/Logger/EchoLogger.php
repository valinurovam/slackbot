<?php

namespace SlackBot\Logger;

use Psr\Log\AbstractLogger;

class EchoLogger extends AbstractLogger
{
    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = [])
    {
        printf(
            "[%s] {%s}: %s\n",
            date('Y-m-d H:i:s'),
            $level,
            str_replace('%', '%%', $message . ($context ? '' : ' ' . json_encode($context)))
        );
    }
}
