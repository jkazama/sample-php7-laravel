<?php

namespace App\Http\Controllers\Auth;

use App\Context\Actor\Actor;
use App\Context\Actor\ActorRoleType;
use App\Http\Controllers\Controller;
use App\Usecases\AccountService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Log;

/**
 * 認証関連のController。
 */
class LoginApiController extends Controller
{
    use AuthenticatesUsers;

    private $service;

    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    /** 入力審査 */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'loginId' => 'required|max:255',
            'password' => 'required|min:6',
        ]);
    }

    /** 認証情報生成 */
    protected function credentials(Request $request)
    {
        return ['name' => $request->loginId, 'password' => $request->password];
    }

     /** ログイン成功処理 */
    protected function authenticated(Request $request, $user)
    {
        $this->service->dh->actorSession->bind(Actor::of($user->name, ActorRoleType::USER));
        Log::info('ログインに成功しました');
        return response()->json('', 200);
    }

    /** ログイン失敗処理 */
    protected function sendFailedLoginResponse(Request $request)
    {
        Log::info('ログインに失敗しました');
        return response()->json('', 400);
    }

    public function logout(Request $request)
    {
        $this->service->dh->actorSession->unbind();
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        Log::info('ログアウトに成功しました');
        return response()->json('', 200);
    }

    protected function guard()
    {
      return Auth::guard('api');
    }
}
