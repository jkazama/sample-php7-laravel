<?php

namespace App\Models\Account;

use App\Context\ErrorKeys;
use App\Utils\Validator;
use Illuminate\Database\Eloquent\Model;

/**
 * 口座ログインを表現します。
 * low: サンプル用に必要最低限の項目だけ
 */
class Login extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    /** ログインIDを変更します。 */
    public function change(string $loginId): Login
    {
        Validator::validate(function ($v) use ($loginId) {
            $empty = self::where('id', '<>', $this->id)
                ->where('loginId', $loginId)
                ->first() == null;
            $v->checkField($empty, 'loginId', ErrorKeys::DUPLICATE_ID);
        });
        $this->loginId = $loginId;
        $this->save();
        return $this;
    }
    /** パスワードを変更します。 */
    public function changePassword(string $plainPassword): Login
    {
        $this->password = self::encode($plainPassword);
        $this->save();
        return $this;
    }
    public static function encode(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT);
    }
    /** ログイン情報を取得します。 */
    public static function getByLoginId(string $loginId): Login
    {
        return self::where('loginId', $loginId)->first();
    }

    /**
     * ログイン情報の登録を行います。
     * @param array [key: id, plainPassword]
     * @return Login
     */
    public static function register(array $p): Login
    {
        $m = new Login();
        $m->id = $p['id'];
        $m->loginId = $p['id'];
        $m->password = self::encode($p['plainPassword']);
        $m->save();
        return $m;
    }

}
