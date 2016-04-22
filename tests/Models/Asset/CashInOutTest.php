<?php
namespace App\Models\Asset;

use App\Context\ActionStatusType;
use App\Context\ErrorKeys;
use App\Context\ValidationException;
use App\Models\DomainErrorKeys;
use App\Tests\EntityTestSupport;

//low: 簡易な正常系検証が中心。依存するCashflow/CashBalanceの単体検証パスを前提。
class CashInOutTest extends EntityTestSupport
{
    const CCY = "JPY";
    const ACC_ID = "test";

    protected function initialize()
    {
        // 残高1000円の口座(test)を用意
        $baseDay = $this->businessDay->day();
        $this->fixtures->selfFiAcc(Remarks::CASH_OUT, self::CCY)->save();
        $this->fixtures->acc(self::ACC_ID)->save();
        $this->fixtures->fiAcc(self::ACC_ID, Remarks::CASH_OUT, self::CCY)->save();
        $this->fixtures->cb(self::ACC_ID, $baseDay, self::CCY, '1000')->save();
    }

    // 振込出金依頼をする
    public function testWithdrawal()
    {
        $baseDay = $this->businessDay->day();
        $basePlus3Day = $this->businessDay->day(3);

        // 超過の出金依頼 [例外]
        try {
            CashInOut::withdraw($this->dh, $this->businessDay, [
                'accountId' => self::ACC_ID, 'currency' => self::CCY, 'absAmount' => 1001,
            ]);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(AssetErrorKeys::CASH_IN_OUT_WITHDRAW_AMOUNT, $e->getMessage());
        }

        // 0円出金の出金依頼 [例外]
        try {
            CashInOut::withdraw($this->dh, $this->businessDay, [
                'accountId' => self::ACC_ID, 'currency' => self::CCY, 'absAmount' => 0,
            ]);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(DomainErrorKeys::ABS_AMOUNT_ZERO, $e->getMessage());
        }

        // 通常の出金依頼
        $normal = CashInOut::withdraw($this->dh, $this->businessDay, [
            'accountId' => self::ACC_ID, 'currency' => self::CCY, 'absAmount' => 300,
        ]);
        $this->assertEquals(self::ACC_ID, $normal->accountId);
        $this->assertEquals(self::CCY, $normal->currency);
        $this->assertEquals(300, $normal->absAmount);
        $this->assertEquals(true, $normal->withdrawal);
        $this->assertEquals($baseDay, $normal->requestDay);
        $this->assertEquals($basePlus3Day, $normal->valueDay);
        $this->assertEquals(Remarks::CASH_OUT . '-' . self::CCY, $normal->targetFiCode);
        $this->assertEquals('FI' . self::ACC_ID, $normal->targetFiAccountId);
        $this->assertEquals(Remarks::CASH_OUT . '-' . self::CCY, $normal->selfFiCode);
        $this->assertEquals('xxxxxx', $normal->selfFiAccountId);
        $this->assertEquals(ActionStatusType::UNPROCESSED, $normal->statusType);
        $this->assertNull($normal->cashflowId);

        // 拘束額を考慮した出金依頼 [例外]
        try {
            CashInOut::withdraw($this->dh, $this->businessDay, [
                'accountId' => self::ACC_ID, 'currency' => self::CCY, 'absAmount' => 701,
            ]);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(AssetErrorKeys::CASH_IN_OUT_WITHDRAW_AMOUNT, $e->getMessage());
        }
    }

    // 振込出金依頼を取消する
    public function testCancel()
    {
        $baseDay = $this->businessDay->day();
        // CF未発生の依頼を取消
        $normal = $this->fixtures->cio(self::ACC_ID, 300, true);
        $normal->save();
        $normal->cancel($this->dh);
        $this->assertEquals(ActionStatusType::CANCELLED, $normal->statusType);

        // 発生日を迎えた場合は取消できない [例外]
        $today = $this->fixtures->cio(self::ACC_ID, 300, true);
        $today->eventDay = $baseDay;
        $today->save();
        try {
            $today->cancel($this->dh);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(AssetErrorKeys::CASH_IN_OUT_BEFORE_EQUALS_DAY, $e->getMessage());
        }
    }

    // 振込出金依頼を例外状態とする
    public function testError()
    {
        $baseDay = $this->businessDay->day();
        // CF未発生の依頼を取消
        $normal = $this->fixtures->cio(self::ACC_ID, 300, true);
        $normal->save();
        $normal->error($this->dh);
        $this->assertEquals(ActionStatusType::ERROR, $normal->statusType);

        // 処理済の時はエラーにできない [例外]
        $today = $this->fixtures->cio(self::ACC_ID, 300, true);
        $today->eventDay = $baseDay;
        $today->statusType = ActionStatusType::PROCESSED;
        $today->save();
        try {
            $today->error($this->dh);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(ErrorKeys::ACTION_UNPROCESSING, $e->getMessage());
        }
    }

    // 発生日を迎えた振込入出金をキャッシュフロー登録する
    public function testProcess()
    {
        $baseDay = $this->businessDay->day();
        $basePlus3Day = $this->businessDay->day(3);

        // 発生日未到来の処理 [例外]
        $future = $this->fixtures->cio(self::ACC_ID, 300, true);
        $future->save();
        try {
            $future->process($this->dh);
        } catch (ValidationException $e) {
            $this->assertEquals(AssetErrorKeys::CASH_IN_OUT_AFTER_EQUALS_DAY, $e->getMessage());
        }

        // 発生日到来処理
        $normal = $this->fixtures->cio(self::ACC_ID, 300, true);
        $normal->eventDay = $baseDay;
        $normal->save();
        $normal->process($this->dh);
        $this->assertEquals(ActionStatusType::PROCESSED, $normal->statusType);
        $this->assertNotNull($normal->cashflowId);
        // 発生させたキャッシュフローの検証
        $m = Cashflow::find($normal->cashflowId);
        $this->assertEquals(self::ACC_ID, $m->accountId);
        $this->assertEquals(self::CCY, $m->currency);
        $this->assertEquals(-300, $m->amount);
        $this->assertEquals(CashflowType::CASH_OUT, $m->cashflowType);
        $this->assertEquals(Remarks::CASH_OUT, $m->remark);
        $this->assertEquals($baseDay, new \DateTime($m->eventDay));
        $this->assertEquals($basePlus3Day, new \DateTime($m->valueDay));
        $this->assertEquals(ActionStatusType::UNPROCESSED, $m->statusType);
    }

}
