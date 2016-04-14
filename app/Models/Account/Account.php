<?php

namespace App\Models\Account;

use App\Context\Actor\ActorRoleType;
use App\Context\ErrorKeys;
use App\Context\ValidationException;
use App\Utils\Validator;
use Illuminate\Database\Eloquent\Model;

/**
 * 口座を表現します。
 * low: サンプル用に必要最低限の項目だけ
 */
class Account extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    public function actor(): Actor
    {
        return new Actor($this->id, $this->name, ActorRoleType::USER);
    }

    /** 口座に紐付くログイン情報を取得します。 */
    public function loadLogin(): Login
    {
        return Login::find($this->id);
    }

    /**
     * 口座を変更します。
     * @param array [key: name, mail]
     * @return Account
     */
    public function change(array $p): Account
    {
        $this->name = $p['name'];
        $this->mail = $p['mail'] ?? '';
        $this->save();
        return $this;
    }

    /** 有効な口座を取得します。(例外付) */
    public static function loadValid(string $id): Account
    {
        $m = self::find($id);
        if ($m == null || !AccountStatusType::valid($m->statusType)) {
            throw ValidationException::of('error.Account.loadValid');
        }
        return $m;
    }

    /**
     * 口座の登録を行います。
     * <p>ログイン情報も同時に登録されます。
     * @param array [key: id, name, mail, plainPassword]
     * @return Account
     */
    public static function register(array $p): Account
    {
        Validator::validate(function ($v) use ($p) {
            $v->checkField(self::find($p['id']) == null, "id", ErrorKeys::DUPLICATE_ID);
        });

        Login::register($p);
        $m = new Account();
        $m->id = $p['id'];
        $m->name = $p['name'];
        $m->mail = $p['mail'];
        $m->statusType = AccountStatusType::NORMAL;
        $m->save();
        return $m;
    }
}
