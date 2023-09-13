<?php

namespace Exception;
use Exception;
class tableException extends Exception
{

    public function getErrorLine(): int
    {
        $except = debug_backtrace();
        $except = end($except)['line'];
        return $except;
    }
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