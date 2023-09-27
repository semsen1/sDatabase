<?php


namespace Tests\Unit;

use Codeception\Stub\Expected;
use database\db;
use database\table;
use Tests\Support\UnitTester;

class dbTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testSomeFeature()
    {
        $base = $this->make(new table,[
           "create"=>Expected::never()
        ]);
        $this->assertSame('',$base->create());
    }
}
