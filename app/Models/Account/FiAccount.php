<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;

/**
 * 口座に紐づく金融機関口座を表現します。
 * <p>口座を相手方とする入出金で利用します。
 * low: サンプルなので支店や名称、名義といった本来必須な情報をかなり省略しています。(通常は全銀仕様を踏襲します)
 */
class FiAccount extends Model
{
    /** 口座に紐づく金融機関口座を取得します。 */
    public static function loadBy(string $accountId, string $category, string $currency): FiAccount
    {
        return self::where('accountId', $accountId)
            ->where('category', $category)
            ->where('currency', $currency)
            ->firstOrFail();
    }
}
