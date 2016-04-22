<?php

namespace App\Http\Controllers;

use App\Models\Asset\CashInOut;
use App\Usecases\AssetService;
use Illuminate\Http\Request;

/**
 * 資産に関わる顧客のUI要求を処理します。
 */
class AssetController extends Controller
{
    private $service;

    public function __construct(AssetService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function withdraw(Request $request)
    {
        $rules = CashInOut::validateWithdrawRules();
        $this->validate($request, $rules);
        return $this->service->withdraw($request->only(array_keys($rules)));
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
