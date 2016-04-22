<?php
namespace App\Models\Asset;

use App\Context\DomainHelper;
use App\Utils\Calculator;

/**
 * 口座の資産概念を表現します。
 * asset配下のEntityを横断的に取り扱います。
 * low: 実際の開発では多通貨や執行中/拘束中のキャッシュフローアクションに対する考慮で、サービスによってはかなり複雑になります。
 */
class Asset
{
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    /** 口座IDに紐付く資産概念を返します。 */
    public static function of(string $accountId): Asset
    {
        return new Asset($accountId);
    }

    /**
     * 振込出金可能か判定します。
     * <p>0 &lt;= 口座残高 + 未実現キャッシュフロー - (出金依頼拘束額 + 出金依頼額)
     * low: 判定のみなのでscale指定は省略。余力金額を返す時はきちんと指定する
     */
    public function canWithdraw(DomainHelper $dh, string $currency, float $absAmount, \DateTimeInterface $valueDay)
    {
        $calc = Calculator::of(CashBalance::getOrNew($dh, $this->id, $currency)->amount);
        Cashflow::findUnrealize($this->id, $currency, $valueDay)->each(function ($cf) use ($calc) {
            $calc->add($cf->amount);
        });
        CashInOut::findUnprocessedByAccountCurrency($this->id, $currency, true)->each(function ($withdrawal) use ($calc) {
            $calc->subtract($withdrawal->absAmount);
        });
        $calc->subtract($absAmount);
        return 0 <= $calc->float();
    }

}
