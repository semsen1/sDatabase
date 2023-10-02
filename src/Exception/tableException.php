<?php

namespace database\Exception;
use Exception;
use database\Exception\getErrorLine;
class tableException extends Exception
{
    use getErrorLine;
    /**
     * @param $message
     * @param $line
     */
    function __construct($message,$code = 0)
    {
        $this->message = $message;
        $this->code = $code;
        $this->line = $this->getErrorLine();

    }
}