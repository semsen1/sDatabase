<?php
namespace database\Exception;
trait getErrorLine
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
}