<?php
namespace App\Usecases;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Asset\CashInOut;

/**
 * 資産ドメインに対する顧客ユースケース処理。
 */
class AssetService
{
    use ServiceSupport;

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
        return $this->tx(function () use ($p) {
            return CashInOut::withdraw($this->dh, $this->sh->businessDay, $p)->id;
        });
    }

    /**
     * 未処理の振込依頼情報を検索します。
     * low: CashInOutは情報過多ですがアプリケーション層では公開対象を特定しにくい事もあり、
     * UI層に最終判断を委ねています。
     */
    public function findUnprocessedCashOut(): Collection
    {
        return CashInOut::findUnprocessedByAccount($this->dh->actor()->id);
    }

}
