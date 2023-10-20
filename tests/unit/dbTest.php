<?php

use PHPUnit\Framework\TestCase;
use database\db;
use database\Exception\dbException;
class dbTest extends TestCase
{
    private $db;
    private $db2;

    public function setUp(): void
    {
        $this->db = new db('postgres12','root','');

    }

    public function testCheckIsSuccess(){
        $this->assertInstanceOf('PDO',$this->db->getDb());
    }

    public function testCheckIsDenial(){
        $this->expectException(dbException::class);
        $this->expectExceptionMessage("authentication failed");
        new db('postgres12','root','af');
    }
}