<?php

namespace Exception;
use Exception;
class tableException extends Exception
{
    /**
     * @param $message
     * @param $line
     */
    function __construct($message, $line)
    {
        $this->message = $message;
        $this->line = $line;

    }
}