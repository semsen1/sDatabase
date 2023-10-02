<?php

namespace database\Exception;
use Exception;
use database\Exception\getErrorLine;
class dbException extends Exception
{
    use getErrorLine;

    /**
     * @param $message
     * @param $line
     */
    function __construct($message)
    {
        $this->message = $message;
        $this->line = $this->getErrorLine();

    }

}