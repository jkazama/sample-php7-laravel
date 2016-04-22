<?php
namespace App\Usecases;

use App\Models\Account\Account;

/**
 * 口座ドメインに対する顧客ユースケース処理。
 */
class AccountService
{
    use ServiceSupport;

    public $sh;
    public $dh;

    public function __construct(ServiceHelper $sh)
    {
        $this->sh = $sh;
        $this->dh = $sh->dh;
    }

    public function getAccount(string $id)
    {
        return Account::loadValid($id);
    }

}
