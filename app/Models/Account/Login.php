<?php

namespace App\Models\Account;

use App\User;

/**
 * 口座ログインのドメイン概念を表現します。
 * <p>内部で User に依存しています。
 */
class Login
{
    public $incrementing = false;
    public $timestamps = false;

    /** ログイン情報を取得します。 */
    public static function loadByAccountId(string $accountId): User
    {
        return User::where('name', $accountId)->firstOrFail();
    }

    /** パスワードを変更します。 */
    public static function changePassword(string $id, string $plainPassword): User
    {
        $m = self::loadByAccountId($id);
        $m->password = self::encode($plainPassword);
        $m->save();
        return $m;
    }
    public static function encode(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    /**
     * ログイン情報の登録を行います。
     * @param array [key: id, plainPassword]
     * @return Login
     */
    public static function register(array $p): User
    {
        return User::create([
            'name' => $p['id'],
            'email' => $p['mail'],
            'password' => self::encode($p['plainPassword']),
        ]);
    }

}
