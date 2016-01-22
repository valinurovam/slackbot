<?php

namespace SlackBot\Response;

interface ResponseInterface
{
    /**
     * Message to send to user or channel
     * @return string
     */
    public function getText();

    /**
     * Return username or channel name like #general or @username
     * @return string
     */
    public function getDestination();
}
