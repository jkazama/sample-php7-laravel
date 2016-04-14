<?php
namespace App\Models\Asset;

/** 摘要定数 */
interface Remarks
{
    /** 振込入金 */
    const CASH_IN = "cashIn";
    /** 振込入金(調整) */
    const CASH_IN_ADJUST = "cashInAdjust";
    /** 振込入金(取消) */
    const CASH_IN_CANCEL = "cashInCancel";
    /** 振込出金 */
    const CASH_OUT = "cashOut";
    /** 振込出金(調整) */
    const CASH_OUT_ADJUST = "cashOutAdjust";
    /** 振込出金(取消) */
    const CASH_OUT_CANCEL = "cashOutCancel";
}
