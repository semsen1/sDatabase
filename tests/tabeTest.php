<?php

namespace database\tests\tabeTest;
use database\table;
use PHPUnit\Framework\TestCase;

class tabeTest extends TestCase
{
    private table $table;
    protected function setUp():void{
        $this->table = new table();
    }
}