<?php

namespace App\Http\Controllers;

use App\Usecases\AssetService;
use Request;

/**
 * 資産に関わる顧客のUI要求を処理します。
 */
class AssetController extends Controller
{
    private $service;

    public function __construct(AssetService $service)
    {
        $this->service = $service;
    }

    public function withdraw()
    {
        //todo validation
        $p = Request::only('currency', 'absAmount');
        return $this->service->withdraw($p);
    }

    /** 未処理の振込依頼情報を検索します。 */
    public function findUnprocessedCashOut()
    {
        return $this->service->findUnprocessedCashOut()->map(function ($m) {
            return [
                'id' => $m['id'],
                'currency' => $m['currency'],
                'absAmount' => $m['absAmount'],
                'requestDay' => $m['requestDay'],
                'requestDate' => $m['requestDate'],
                'eventDay' => $m['eventDay'],
                'valueDay' => $m['valueDay'],
                'statusType' => $m['statusType'],
                'updateDate' => $m['updateDate'],
                'cashflowId' => $m['cashflowId'],
            ];
        });
    }
}
