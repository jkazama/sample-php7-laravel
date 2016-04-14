<?php
namespace App\Models\Asset;

/** キャッシュフロー種別。 low: 各社固有です。 */
interface CashflowType
{
    /** 振込入金 */
    const CASH_IN = "CashIn";
    /** 振込出金 */
    const CASH_OUT = "CashOut";
    /** 振替入金 */
    const CASH_TRANSFER_IN = "CashTransferIn";
    /** 振替出金 */
    const CASH_TRANSFER_OUT = "CashTransferOut";
}
