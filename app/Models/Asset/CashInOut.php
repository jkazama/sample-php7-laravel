<?php
namespace App\Models\Asset;

use App\Context\ActionStatusType;
use App\Context\DomainHelper;
use App\Context\ErrorKeys;
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
    public function process(DomainHelper $dh)
    {
        //low: 出金営業日の取得。ここでは単純な営業日を取得
        $now = $dh->time->tp();
        // 事前審査
        Validator::validate(function ($v) {
            $v->verify(ActionStatusType::isUnprocessed($this->statusType), ErrorKeys::ACTION_UNPROCESSING);
            $v->verify($now['day'] <= $this->eventDay, AssetErrorKeys::CASH_IN_OUT_AFTER_EQUALS_DAY);
        });
        // 処理済状態を反映
        $this->statusType = ActionStatusType::PROCESSED;
        $this->save();
    }
    public static function findUnprocessed(string $accountId): array
    {
        return [];
    }
}
