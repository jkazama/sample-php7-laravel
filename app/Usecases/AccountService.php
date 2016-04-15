<?php
namespace App\Usecases;

/**
 * 口座ドメインに対する顧客ユースケース処理。
 */
class AccountService
{
    public $sh;
    public $dh;

    public function __construct(ServiceHelper $sh)
    {
        $this->sh = $sh;
        $this->dh = $sh->dh;
    }

    public function getLoginByLoginId(string $loginId)
    {
        return null;
        //return Login::getByLoginId($dh, $loginId);
    }

    public function getAccount(string $id)
    {
        return null;
        //return Account::getValid($dh, $id);
    }

}
