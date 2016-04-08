<?php
namespace App\Models\Account;

use EntityTestSupport;

class FiAccountTest extends EntityTestSupport
{
    protected function initialize()
    {
        $this->fixtures->fiAcc('normal', 'sample', 'JPY')->save();
    }

    public function testLoadBy()
    {
        $m = FiAccount::loadBy('normal', 'sample', 'JPY');
        $this->assertTrue(isset($m));
        $this->assertEquals('normal', $m->accountId);
        $this->assertEquals('sample', $m->category);
        $this->assertEquals('JPY', $m->currency);
        $this->assertEquals('sample-JPY', $m->fiCode);
        $this->assertEquals('FInormal', $m->fiAccountId);
    }
}
