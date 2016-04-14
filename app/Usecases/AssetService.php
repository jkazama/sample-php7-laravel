<?php
namespace App\Usecases;

use App\Models\Asset\CashInOut;

/**
 * 資産ドメインに対する顧客ユースケース処理。
 */
class AssetService
{
    private $sh;
    private $dh;

    public function __construct(ServiceHelper $sh)
    {
        $this->sh = $sh;
        $this->dh = $sh->dh;
    }

    /**
     * 未処理の振込依頼情報を検索します。
     * low: 参照系は口座ロックが必要無いケースであれば@Transactionalでも十分
     * low: CashInOutは情報過多ですがアプリケーション層では公開対象を特定しにくい事もあり、
     * UI層に最終判断を委ねています。
     */
    public function findUnprocessedCashOut(): array
    {
        $actor = $this->dh->actor();
        //return CashInOut::findUnprocessed($actor->id);
        return CashInOut::findUnprocessed('sample');
    }

}
