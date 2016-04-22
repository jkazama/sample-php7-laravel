<?php

namespace App\Http\Controllers;

use App\Context\Actor\Actor;
use App\Usecases\AccountService;

/**
 * 口座に関わる顧客のUI要求を処理します。
 */
class AccountController extends Controller
{
    private $service;

    public function __construct(AccountService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function loginStatus()
    {
        return ['result' => true];
    }

    public function loadLoginAccount()
    {
        return response()->json($this->service->dh->actor());
    }

}
