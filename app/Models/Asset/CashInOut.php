<?php
namespace App\Models\Asset;

use App\Context\ActionStatusType;
use App\Context\DomainHelper;
use App\Context\ErrorKeys;
use App\Models\Account\FiAccount;
use App\Models\BusinessDayHandler;
use App\Models\DomainErrorKeys;
use App\Models\Master\SelfFiAccount;
use App\Utils\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * 振込入出金依頼を表現するキャッシュフローアクション。
 * <p>相手方/自社方の金融機関情報は依頼後に変更される可能性があるため、依頼時点の状態を
 * 保持するために非正規化して情報を保持しています。
 * low: 相手方/自社方の金融機関情報は項目数が多いのでサンプル用に金融機関コードのみにしています。
 * 実際の開発ではそれぞれ複合クラス(FinantialInstitution)に束ねるアプローチを推奨します。
 */
class CashInOut extends Model
{
    public $timestamps = false;

    /**
     * 依頼を処理します。
     * <p>依頼情報を処理済にしてキャッシュフローを生成します。
     */
    public function process(DomainHelper $dh): CashInOut
    {
        //low: 出金営業日の取得。ここでは単純な営業日を取得
        $now = $dh->time->tp();
        // 事前審査
        Validator::validate(function ($v) use ($now) {
            $v->verify(ActionStatusType::isUnprocessed($this->statusType), ErrorKeys::ACTION_UNPROCESSING);
            $v->verify($now['day'] <= $this->eventDay, AssetErrorKeys::CASH_IN_OUT_AFTER_EQUALS_DAY);
        });
        // 処理済状態を反映
        $this->statusType = ActionStatusType::PROCESSED;
        $this->cashflowId = Cashflow::register($dh, $this->regCf())->id;
        $this->createDate = $now['date'];
        $this->createId = $dh->actor()->id;
        $this->updateDate = $now['date'];
        $this->updateId = $dh->actor()->id;
        $this->save();
        return $this;
    }

    private function regCf(): array
    {
        if ($this->withdrawal) {
            $amount = $this->absAmount * -1;
            $cashflowType = CashflowType::CASH_OUT;
            $remark = Remarks::CASH_OUT;
        } else {
            $amount = $this->absAmount;
            $cashflowType = CashflowType::CASH_IN;
            $remark = Remarks::CASH_IN;
        }
        return [
            'accountId' => $this->accountId, 'currency' => $this->currency,
            'amount' => $amount, 'cashflowType' => $cashflowType, 'remark' => $remark,
            'eventDay' => $this->eventDay, 'valueDay' => $this->valueDay,
        ];
    }

    /**
     * 依頼を取消します。
     * <p>"処理済みでない"かつ"発生日を迎えていない"必要があります。
     */
    public function cancel(DomainHelper $dh): CashInOut
    {
        $now = $dh->time->tp();
        // 事前審査
        Validator::validate(function ($v) use ($now) {
            $v->verify(ActionStatusType::isUnprocessing($this->statusType), ErrorKeys::ACTION_UNPROCESSING);
            $v->verify($now['day'] < $this->eventDay, AssetErrorKeys::CASH_IN_OUT_BEFORE_EQUALS_DAY);
        });
        // 取消状態を反映
        $this->statusType = ActionStatusType::CANCELLED;
        $this->createDate = $now['date'];
        $this->createId = $dh->actor()->id;
        $this->updateDate = $now['date'];
        $this->updateId = $dh->actor()->id;
        $this->save();
        return $this;
    }

    /**
     * 依頼をエラー状態にします。
     * <p>処理中に失敗した際に呼び出してください。
     * low: 実際はエラー事由などを引数に取って保持する
     */
    public function error(DomainHelper $dh): CashInOut
    {
        // 事前審査
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

    /** 当日発生で未処理の振込入出金一覧を検索します。 */
    public static function findUnprocessed(DomainHelper $dh): Collection
    {
        return self::where('eventDay', $dh->time->day())
            ->whereIn('statusType', ActionStatusType::unprocessedTypes())
            ->orderBy('id')
            ->get();
    }

    /** 未処理の振込入出金一覧を検索します。(口座別) */
    public static function findUnprocessedByAccountCurrency(string $accountId, string $currency, bool $withdrawal): Collection
    {
        return self::where('accountId', $accountId)
            ->where('currency', $currency)
            ->where('withdrawal', $withdrawal)
            ->whereIn('statusType', ActionStatusType::unprocessedTypes())
            ->orderBy('id')
            ->get();
    }

    /** 未処理の振込入出金一覧を検索します。(口座別) */
    public static function findUnprocessedByAccount(string $accountId): Collection
    {
        return self::where('accountId', $accountId)
            ->whereIn('statusType', ActionStatusType::unprocessedTypes())
            ->orderBy('updateDate', 'desc')
            ->get();
    }

    /** 出金依頼をします。 */
    public static function withdraw(DomainHelper $dh, BusinessDayHandler $day, array $p): CashInOut
    {
        $now = $dh->time->tp();
        // low: 発生日は締め時刻等の兼ね合いで営業日と異なるケースが多いため、別途DB管理される事が多い
        $eventDay = $day->day();
        // low: 実際は各金融機関/通貨の休日を考慮しての T+N 算出が必要
        $valueDay = $day->day(3);

        $accountId = $p['accountId'];
        $currency = $p['currency'];
        $absAmount = $p['absAmount'];

        // 事前審査
        Validator::validate(function ($v) use ($dh, $accountId, $currency, $absAmount, $valueDay) {
            $v->verifyField(0 < $absAmount, "absAmount", DomainErrorKeys::ABS_AMOUNT_ZERO);
            $canWithdraw = Asset::of($accountId)->canWithdraw($dh, $currency, $absAmount, $valueDay);
            $v->verifyField($canWithdraw, 'absAmount', AssetErrorKeys::CASH_IN_OUT_WITHDRAW_AMOUNT);
        });

        // 出金依頼情報を登録
        $acc = FiAccount::loadBy($accountId, Remarks::CASH_OUT, $currency);
        $selfAcc = SelfFiAccount::loadBy(Remarks::CASH_OUT, $currency);
        $m = new CashInOut();
        $m->accountId = $accountId;
        $m->currency = $currency;
        $m->absAmount = $absAmount;
        $m->withdrawal = true;
        $m->requestDay = $now['day'];
        $m->requestDate = $now['date'];
        $m->eventDay = $eventDay;
        $m->valueDay = $valueDay;
        $m->targetFiCode = $acc->fiCode;
        $m->targetFiAccountId = $acc->fiAccountId;
        $m->selfFiCode = $selfAcc->fiCode;
        $m->selfFiAccountId = $selfAcc->fiAccountId;
        $m->statusType = ActionStatusType::UNPROCESSED;
        $m->createDate = $now['date'];
        $m->createId = $dh->actor()->id;
        $m->updateDate = $now['date'];
        $m->updateId = $dh->actor()->id;
        $m->save();
        return $m;
    }

    public static function validateWithdrawRules()
    {
        return [
            'currency' => 'required',
            'absAmount' => 'required|numeric',
        ];
    }

}
