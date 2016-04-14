<?php
namespace App\Models\Master;

use App\Tests\EntityTestSupport;

class SelfFiAccountTest extends EntityTestSupport
{
    protected function initialize()
    {
        $this->fixtures->selfFiAcc('sample', 'JPY')->save();
    }

    public function testLoadBy()
    {
        $m = SelfFiAccount::loadBy('sample', 'JPY');
        $this->assertTrue(isset($m));
        $this->assertEquals('sample', $m->category);
        $this->assertEquals('JPY', $m->currency);
        $this->assertEquals('sample-JPY', $m->fiCode);
        $this->assertEquals('xxxxxx', $m->fiAccountId);
    }
}
