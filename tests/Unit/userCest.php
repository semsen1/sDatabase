<?php


namespace Tests\Unit;

use database\db;
use database\table;
use Tests\Support\UnitTester;
use PDO;

class userCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function tryToTest(UnitTester $I)
    {
       $db =  new db("laravel",'root','');
       $I->assertInstanceOf("PDO",$db->getDb());
    }
}
