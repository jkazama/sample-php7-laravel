<?php
namespace App\Models\Account;

/** 口座状態を表現します。 */
class AccountStatusType
{
    /** 通常 */
    const NORMAL = "Normal";
    /** 退会 */
    const WITHDRAWAL = "Withdrawal";

    public static function valid(string $type): bool
    {
        return $type === self::NORMAL;
    }
    public static function invalid(): bool
    {
        return !valid();
    }
}
