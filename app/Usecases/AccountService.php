<?php
namespace App\Usecases;

use App\Context\DomainHelper;

/**
 * 口座ドメインに対する顧客ユースケース処理。
 */
class AccountService
{
    private $dh;

    public function __construct(DomainHelper $dh)
    {
        $this->dh = $dh;
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
