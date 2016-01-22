<?php

namespace SlackBot\Response;

class Response implements ResponseInterface
{
    /**
     * @var string
     */
    protected $text;
    /**
     * @var string
     */
    protected $destination;

    /**
     * Response constructor.
     * @param string $text
     * @param string $destinatino
     */
    public function __construct($text, $destinatino)
    {
        $this->text = $text;
        $this->destination = $destinatino;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }


}
