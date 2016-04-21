<?php
namespace App\Models\Asset;

/** 審査例外で用いるメッセージキー定数 */
interface AssetErrorKeys
{
    /** 受渡日を迎えていないため実現できません */
    const CASHFLOW_REALIZE_DAY = 'validation.Cashflow.realizeDay';
    /** 既に受渡日を迎えています */
    const CASHFLOW_BEFORE_EQUALS_DAY = 'validation.Cashflow.beforeEqualsDay';

    /** 未到来の受渡日です */
    const CASH_IN_OUT_AFTER_EQUALS_DAY = 'validation.CashInOut.afterEqualsDay';
    /** 既に発生日を迎えています */
    const CASH_IN_OUT_BEFORE_EQUALS_DAY = 'validation.CashInOut.beforeEqualsDay';
    /** 出金可能額を超えています */
    const CASH_IN_OUT_WITHDRAW_AMOUNT = 'validation.CashInOut.withdrawAmount';
}
