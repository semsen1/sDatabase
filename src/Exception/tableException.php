<?php

namespace Exception;
use Exception;
use ErrorInterface;
class tableException extends Exception
{
    /**
     * @return int
     */
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