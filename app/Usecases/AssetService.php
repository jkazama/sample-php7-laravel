<?php
namespace App\Usecases;

use App\Models\Asset\CashInOut;
use Illuminate\Database\Eloquent\Collection;

/**
 * 資産ドメインに対する顧客ユースケース処理。
 */
class AssetService
{
    public $sh;
    public $dh;

    public function __construct(ServiceHelper $sh)
    {
        $this->sh = $sh;
        $this->dh = $sh->dh;
    }

    public function withdraw(array $p)
    {
        $p['accountId'] = $this->dh->actor()->id;
        return CashInOut::withdraw($this->dh, $this->sh->businessDay, $p)->id;
    }

    /**
     * 未処理の振込依頼情報を検索します。
     * low: 参照系は口座ロックが必要無いケースであれば@Transactionalでも十分
     * low: CashInOutは情報過多ですがアプリケーション層では公開対象を特定しにくい事もあり、
     * UI層に最終判断を委ねています。
     */
    public function findUnprocessedCashOut(): Collection
    {
        return CashInOut::findUnprocessedByAccount($this->dh->actor()->id);
    }

}
