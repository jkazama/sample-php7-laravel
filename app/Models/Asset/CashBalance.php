<?php
namespace App\Models\Asset;

use App\Context\DomainHelper;
use App\Utils\Calculator;
use Illuminate\Database\Eloquent\Model;

/**
 * 口座残高を表現します。
 */
class CashBalance extends Model
{
    public $timestamps = false;

    // 日付/日時は文字列でなく DateTime で取得するよう定義
    protected $dates = ['baseDay', 'updateDate'];
    
    /**
     * 残高へ指定した金額を反映します。
     * low 実際の通貨桁数や端数処理定義はDBや設定ファイル等で管理されます。
     */
    public function add(float $addAmount, $scale = 0): CashBalance
    {
        $mode = Calculator::ROUNDING_DOWN;
        $this->amount = Calculator::of($this->amount)
            ->scale($scale, $mode)->add($addAmount)->float();
        $this->save();
        return $this;
    }

    /**
     * 指定口座の残高を取得します。(存在しない時は繰越保存後に取得します)
     * low: 複数通貨の適切な考慮や細かい審査は本筋でないので割愛。
     */
    public static function getOrNew(DomainHelper $dh, string $accountId, string $currency): CashBalance
    {
        $day = $dh->time->day();
        $m = self::where('accountId', $accountId)
            ->where('currency', $currency)
            ->where('baseDay', $day)
            ->orderBy('baseDay', 'desc')
            ->first();
        return $m ?? self::register($dh, $accountId, $currency);
    }

    private static function register(DomainHelper $dh, string $accountId, string $currency): CashBalance
    {
        $now = $dh->time->tp();
        $m = self::where('accountId', $accountId)
            ->where('currency', $currency)
            ->orderBy('baseDay', 'desc')
            ->first();
        $cb = new CashBalance();
        $cb->accountId = $accountId;
        $cb->baseDay = $now['day'];
        $cb->currency = $currency;
        $cb->amount = isset($m) ? $m->amount : 0;
        $cb->updateDate = $now['date'];
        $cb->save();
        return self::find($cb->id);
    }

}
