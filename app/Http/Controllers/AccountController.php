<?php

namespace App\Http\Controllers;

use App\Models\Account\Account;
use App\Usecases\AccountService;

/**
 * 口座に関わる顧客のUI要求を処理します。
 */
class AccountController extends Controller
{
    private $service;

    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    public function loginStatus()
    {
        //Account::register(['id' => 'hi', 'name' => 'hogagagaa', 'mail' => 'a@sample.com']);
        Account::findOrFail('hi')->change(['name' => 'hoge', 'mail' => 'a@sample.com']);
        return Account::findOrFail('hi');
    }

    public function loadLoginAccount()
    {
        //TODO: セッション中のログイン情報を取得
        return [
            "id" => "sample",
            "name" => "sample",
            "authorities" => [],
        ];
    }

}
