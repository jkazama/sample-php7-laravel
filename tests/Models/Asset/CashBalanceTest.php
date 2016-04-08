<?php
namespace App\Models\Asset;

use EntityTestSupport;

class CashBalanceTest extends EntityTestSupport
{
    public function testAdd()
    {
        $baseDay = $this->businessDay->day();
        $m = $this->fixtures->cb('test1', $baseDay, 'USD', 10.02);
        $m->save();
        // 10.02 + 11.51 = 21.53
        $this->assertEquals('21.53', $m->add(11.51, 2)->amount);
        // 21.53 + 11.516 = 33.04 (端数切捨確認)
        $this->assertEquals('33.04', $m->add(11.516, 2)->amount);
        // 33.04 - 41.51 = -8.47 (マイナス値/マイナス残許容)
        $this->assertEquals('-8.47', $m->add(-41.51, 2)->amount);
    }

    public function testGetOrNew()
    {
        $baseDay = $this->businessDay->day();
        $baseMinus1Day = $this->businessDay->day(-1);
        $this->fixtures->cb('test1', $baseDay, 'JPY', 1000)->save();
        $this->fixtures->cb('test2', $baseMinus1Day, 'JPY', 3000)->save();

        // 存在している残高の検証
        $cbNormal = CashBalance::getOrNew($this->dh, 'test1', 'JPY');
        $this->assertEquals('test1', $cbNormal->accountId);
        $this->assertEquals($baseDay, new \DateTime($cbNormal->baseDay));
        $this->assertEquals('1000', $cbNormal->amount);

        // 基準日に存在していない残高の繰越検証
        $cbRoll = CashBalance::getOrNew($this->dh, "test2", "JPY");
        $this->assertEquals('test2', $cbRoll->accountId);
        $this->assertEquals($baseDay, new \DateTime($cbRoll->baseDay));
        $this->assertEquals('3000', $cbRoll->amount);

        // 残高を保有しない口座の生成検証
        $cbNew = CashBalance::getOrNew($this->dh, "test3", "JPY");
        $this->assertEquals('test3', $cbNew->accountId);
        $this->assertEquals($baseDay, new \DateTime($cbNew->baseDay));
        $this->assertEquals('0', $cbNew->amount);
    }
}
