<?php
namespace App\Models\Asset;

use App\Context\ActionStatusType;
use App\Context\ErrorKeys;
use App\Context\ValidationException;
use App\Tests\EntityTestSupport;

//low: 簡易な正常系検証が中心。依存するCashBalanceの単体検証パスを前提。
class CashflowTest extends EntityTestSupport
{
    public function testRegister()
    {
        $baseDay = $this->businessDay->day();
        $baseMinus1Day = $this->businessDay->day(-1);
        $basePlus1Day = $this->businessDay->day(1);
        // 過去日付の受渡でキャッシュフロー発生 [例外]
        try {
            Cashflow::register($this->dh, [
                'accountId' => 'test1', 'amount' => '1000', 'currency' => 'JPY',
                'cashflowType' => 'CashIn', 'remark' => 'cashIn', 'valueDay' => $baseMinus1Day,
            ]);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(AssetErrorKeys::CASHFLOW_BEFORE_EQUALS_DAY, $e->getMessage());
        }
        // 翌日受渡でキャッシュフロー発生
        $m = Cashflow::register($this->dh, [
            'accountId' => 'test1', 'amount' => '1000', 'currency' => 'JPY',
            'cashflowType' => 'CashIn', 'remark' => 'cashIn', 'valueDay' => $basePlus1Day,
        ]);
        $this->assertEquals(1000, $m->amount);
        $this->assertEquals(ActionStatusType::UNPROCESSED, $m->statusType);
        $this->assertEquals($baseDay, $m->eventDay);
        $this->assertEquals($basePlus1Day, $m->valueDay);
    }

    /** 未実現キャッシュフローを実現する */
    public function testRealize()
    {
        $baseDay = $this->businessDay->day();
        $baseMinus1Day = $this->businessDay->day(-1);
        $baseMinus2Day = $this->businessDay->day(-2);
        $basePlus1Day = $this->businessDay->day(1);

        CashBalance::getOrNew($this->dh, 'test1', 'JPY');

        // 未到来の受渡日 [例外]
        $cfFuture = $this->fixtures->cf('test1', '1000', $baseDay, $basePlus1Day);
        $cfFuture->save();
        try {
            $cfFuture->realize($this->dh);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(AssetErrorKeys::CASHFLOW_REALIZE_DAY, $e->getMessage());
        }

        // キャッシュフローの残高反映検証。  0 + 1000 = 1000
        $cfNormal = $this->fixtures->cf('test1', '1000', $baseMinus1Day, $baseDay);
        $cfNormal->save();
        $cfNormal->realize($this->dh);
        $this->assertEquals(ActionStatusType::PROCESSED, $cfNormal->statusType);
        $cbNormal = CashBalance::getOrNew($this->dh, 'test1', 'JPY');
        $this->assertEquals(1000, $cbNormal->amount);

        // 処理済キャッシュフローの再実現 [例外]
        try {
            $cfNormal->realize($this->dh);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(ErrorKeys::ACTION_UNPROCESSING, $e->getMessage());
        }

        // 過日キャッシュフローの残高反映検証。 1000 + 2000 = 3000
        $cfPast = $this->fixtures->cf('test1', '2000', $baseMinus2Day, $baseMinus1Day);
        $cfPast->save();
        $cfPast->realize($this->dh);
        $this->assertEquals(ActionStatusType::PROCESSED, $cfPast->statusType);
        $cbPast = CashBalance::getOrNew($this->dh, 'test1', 'JPY');
        $this->assertEquals(3000, $cbPast->amount);
    }

    /** 発生即実現のキャッシュフローを登録する */
    public function testRealizeNow()
    {
        $baseDay = $this->businessDay->day();
        CashBalance::getOrNew($this->dh, 'test1', 'JPY');
        // 発生即実現
        Cashflow::register($this->dh, [
            'accountId' => 'test1', 'amount' => '1000', 'currency' => 'JPY',
            'cashflowType' => 'CashIn', 'remark' => 'cashIn', 'valueDay' => $baseDay,
        ]);
        $cb = CashBalance::getOrNew($this->dh, 'test1', 'JPY');
        $this->assertEquals(1000, $cb->amount);
    }
}
