<?php
namespace App\Models\Asset;

use App\Context\ActionStatusType;
use App\Context\DomainHelper;
use App\Context\ErrorKeys;
use App\Utils\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * 入出金キャッシュフローを表現します。
 * キャッシュフローは振込/振替といったキャッシュフローアクションから生成される確定状態(依頼取消等の無い)の入出金情報です。
 * low: 概念を伝えるだけなので必要最低限の項目で表現しています。
 * low: 検索関連は主に経理確認や帳票等での利用を想定します
 */
class Cashflow extends Model
{
    public $timestamps = false;
    
    // 日付/日時は文字列でなく DateTime で取得するよう定義
    protected $dates = ['eventDay', 'eventDate', 'valueDay', 'createDate', 'updateDate'];

    /** キャッシュフローを処理済みにして残高へ反映します。 */
    public function realize(DomainHelper $dh): Cashflow
    {
        Validator::validate(function ($v) use ($dh) {
            $v->verify($this->canRealize($dh), AssetErrorKeys::CASHFLOW_REALIZE_DAY);
            $v->verify(ActionStatusType::isUnprocessing($this->statusType), ErrorKeys::ACTION_UNPROCESSING);
        });
        $date = $dh->time->date();
        $this->statusType = ActionStatusType::PROCESSED;
        $this->createDate = $date;
        $this->createId = $dh->actor()->id;
        $this->updateDate = $date;
        $this->updateId = $dh->actor()->id;
        $this->save();
        CashBalance::getOrNew($dh, $this->accountId, $this->currency)->add($this->amount);
        return $this;
    }

    /**
     * キャッシュフローをエラー状態にします。
     * <p>処理中に失敗した際に呼び出してください。
     * low: 実際はエラー事由などを引数に取って保持する
     */
    public function error(DomainHelper $dh): Cashflow
    {
        Validator::validate(function ($v) {
            $v->verify(ActionStatusType::isUnprocessed($this->statusType), ErrorKeys::ACTION_UNPROCESSING);
        });
        $date = $dh->time->date();
        $this->statusType = ActionStatusType::ERROR;
        $this->createDate = $date;
        $this->createId = $dh->actor()->id;
        $this->updateDate = $date;
        $this->updateId = $dh->actor()->id;
        $this->save();
        return $this;
    }

    /** キャッシュフローを実現(受渡)可能か判定します。 */
    public function canRealize(DomainHelper $dh): bool
    {
        return $this->valueDay <= $dh->time->day();
    }

    /** 指定受渡日時点で未実現のキャッシュフロー一覧を検索します。(口座通貨別) */
    public static function findUnrealize(string $accountId, string $currency, \DateTimeInterface $valueDay): Collection
    {
        return self::where('accountId', $accountId)
            ->where('currency', $currency)
            ->where('valueDay', '<=', $valueDay)
            ->whereIn('statusType', ActionStatusType::unprocessingTypes())
            ->orderBy('id')
            ->get();
    }

    /** 指定受渡日で実現対象となるキャッシュフロー一覧を検索します。 */
    public static function findDoRealize(\DateTimeInterface $valueDay): Collection
    {
        return self::where('valueDay', '<=', $valueDay)
            ->whereIn('statusType', ActionStatusType::unprocessedTypes())
            ->orderBy('id')
            ->get();
    }

    /**
     * キャッシュフローを登録します。
     * 受渡日を迎えていた時はそのまま残高へ反映します。
     */
    public static function register(DomainHelper $dh, array $p): Cashflow
    {
        $now = $dh->time->tp();
        Validator::validate(function ($v) use ($p, $now) {
            $v->checkField($now['day'] <= $p['valueDay'], 'valueDay',
                AssetErrorKeys::CASHFLOW_BEFORE_EQUALS_DAY);
        });
        $eventDay = $p['eventDay'] ?? $now['day'];
        $m = new Cashflow();
        $m->accountId = $p['accountId'];
        $m->currency = $p['currency'];
        $m->amount = $p['amount'];
        $m->cashflowType = $p['cashflowType'];
        $m->remark = $p['remark'];
        $m->eventDay = $eventDay;
        $m->eventDate = $now['date'];
        $m->valueDay = $p['valueDay'];
        $m->statusType = ActionStatusType::UNPROCESSED;
        $m->createDate = $now['date'];
        $m->createId = $dh->actor()->id;
        $m->updateDate = $now['date'];
        $m->updateId = $dh->actor()->id;
        $m->save();
        return $m->canRealize($dh) ? $m->realize($dh) : $m;
    }

}
