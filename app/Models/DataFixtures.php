<?php
namespace App\Models;

use App\Context\Timestamper;
use App\Models\Account\Account;
use App\Models\Account\AccountStatusType;
use App\Models\Account\FiAccount;
use App\Models\Account\Login;
use App\Models\Asset\CashBalance;
use App\Models\BusinessDayHandler;

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
    public function cb(string $accountId, \DateTimeImmutable $baseDay, string $currency, float $amount)
    {
        $m = new CashBalance();
        $m->accountId = $accountId;
        $m->baseDay = $baseDay;
        $m->currency = $currency;
        $m->amount = $amount;
        $m->updateDate = $this->time->day();
        return $m;
    }

}
