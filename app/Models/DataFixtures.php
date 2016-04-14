<?php
namespace App\Models;

use App\Context\ActionStatusType;
use App\Context\Timestamper;
use App\Models\Account\Account;
use App\Models\Account\AccountStatusType;
use App\Models\Account\FiAccount;
use App\Models\Account\Login;
use App\Models\Asset\CashBalance;
use App\Models\Asset\Cashflow;
use App\Models\Asset\CashInOut;
use App\Models\BusinessDayHandler;
use App\Models\Master\SelfFiAccount;

/**
 * データ生成用のサポートコンポーネント。
 * <p>テストや開発時の簡易マスタデータ生成を目的としているため本番での利用は想定していません。
 */
class DataFixtures
{
    private $time;
    private $businessDay;

    public function __construct(Timestamper $time = null)
    {
        $this->time = $time ?? new Timestamper();
        $this->businessDay = new BusinessDayHandler($this->time);
    }

    public function initialize()
    {
        $ccy = 'JPY';
    }

    // account

    /** 口座の簡易生成 */
    public function acc(string $id): Account
    {
        $m = new Account();
        $m->id = $id;
        $m->name = $id;
        $m->mail = 'hoge@example.com';
        $m->statusType = AccountStatusType::NORMAL;
        return $m;
    }

    public function login(string $id): Login
    {
        $m = new Login();
        $m->id = $id;
        $m->loginId = $id;
        $m->password = Login::encode($id);
        return $m;
    }

    /** 口座に紐付く金融機関口座の簡易生成 */
    public function fiAcc(string $accountId, string $category, string $currency): FiAccount
    {
        $m = new FiAccount();
        $m->accountId = $accountId;
        $m->category = $category;
        $m->currency = $currency;
        $m->fiCode = $category . '-' . $currency;
        $m->fiAccountId = 'FI' . $accountId;
        return $m;
    }

    // asset

    /** 口座残高の簡易生成 */
    public function cb(string $accountId, \DateTimeImmutable $baseDay, string $currency, float $amount): CashBalance
    {
        $m = new CashBalance();
        $m->accountId = $accountId;
        $m->baseDay = $baseDay;
        $m->currency = $currency;
        $m->amount = $amount;
        $m->updateDate = $this->time->day();
        return $m;
    }

    /** キャッシュフローの簡易生成 */
    public function cf(string $accountId, float $amount, \DateTimeImmutable $eventDay, \DateTimeImmutable $valueDay): Cashflow
    {
        $date = $this->time->date();
        $m = new Cashflow();
        $m->accountId = $accountId;
        $m->currency = 'JPY';
        $m->amount = $amount;
        $m->cashflowType = 'CASH_IN';
        $m->remark = 'cashIn';
        $m->eventDay = $eventDay;
        $m->eventDate = $this->time->day();
        $m->valueDay = $valueDay;
        $m->statusType = ActionStatusType::UNPROCESSED;
        $m->createDate = $date;
        $m->createId = 'sample';
        $m->updateDate = $date;
        $m->updateId = 'sample';
        return $m;
    }

    /** 振込入出金依頼の簡易生成 [発生日(T+1)/受渡日(T+3)] */
    public function cio(string $accountId, float $absAmount, bool $withdrawal): CashInOut
    {
        $date = $this->time->date();
        $m = new CashInOut();
        $m->accountId = $accountId;
        $m->currency = 'JPY';
        $m->absAmount = $absAmount;
        $m->withdrawal = $withdrawal;
        $m->requestDay = $date;
        $m->requestDate = $date;
        $m->eventDay = $this->businessDay->day(1);
        $m->valueDay = $this->businessDay->day(3);
        $m->targetFiCode = 'tFiCode';
        $m->targetFiAccountId = 'tFiAccId';
        $m->selfFiCode = 'sFiCode';
        $m->selfFiAccountId = 'sFiAccId';
        $m->statusType = ActionStatusType::UNPROCESSED;
        $m->createDate = $date;
        $m->createId = 'sample';
        $m->updateDate = $date;
        $m->updateId = 'sample';
        return $m;
    }

    // master

    /** 自社金融機関口座の簡易生成 */
    public function selfFiAcc(String $category, String $currency): SelfFiAccount
    {
        $m = new SelfFiAccount();
        $m->category = $category;
        $m->currency = $currency;
        $m->fiCode = $category . "-" . $currency;
        $m->fiAccountId = "xxxxxx";
        return $m;
    }
}
