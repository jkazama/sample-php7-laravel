<?php

namespace App\Http\Controllers;

use App\Models\Asset\CashInOut;
use App\Usecases\AssetService;
use Illuminate\Http\Request;
use App\Utils\Formatter;

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
                'requestDay' => Formatter::dateStr($m['requestDay']),
                'requestDate' => Formatter::dateStr($m['requestDate']),
                'eventDay' => Formatter::dateStr($m['eventDay']),
                'valueDay' => Formatter::dateStr($m['valueDay']),
                'statusType' => $m['statusType'],
                'updateDate' => Formatter::dateStr($m['updateDate']),
                'cashflowId' => $m['cashflowId'],
            ];
        });
    }
}
