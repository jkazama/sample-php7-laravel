<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

/**
 * サービス事業者の決済金融機関を表現します。
 * low: サンプルなので支店や名称、名義といったなど本来必須な情報をかなり省略しています。(通常は全銀仕様を踏襲します)
 */
class SelfFiAccount extends Model
{
    public static function loadBy(string $category, string $currency): SelfFiAccount
    {
        return self::where('category', $category)
            ->where('currency', $currency)
            ->firstOrFail();
    }
}
