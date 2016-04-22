<?php

namespace App\Http\Controllers\Auth;

use App\Context\Actor\Actor;
use App\Context\Actor\ActorRoleType;
use App\Http\Controllers\Controller;
use App\Usecases\AccountService;
use Auth;
use Illuminate\Http\Request;
use Log;

/**
 * 認証関連のController。
 */
class AuthController extends Controller
{
    private $service;

    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'loginId' => 'required|max:255',
            'password' => 'required|min:6',
        ]);
        if (Auth::attempt(['name' => $request->loginId, 'password' => $request->password])) {
            $this->service->dh->actorSession->bind(Actor::of($request->loginId, ActorRoleType::USER));
            Log::info('ログインに成功しました');
            return response()->json('', 200);
        } else {
            Log::info('ログインに失敗しました');
            return response()->json('', 401);
        }
    }

    public function logout()
    {
        $this->service->dh->actorSession->unbind();
        Auth::logout();
        Log::info('ログアウトに成功しました');
    }
}
