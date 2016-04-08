<?php
namespace App\Context\Actor;

/**
 * 利用者の役割を表現します。
 */
class ActorRoleType
{
    /** 匿名利用者(ID等の特定情報を持たない利用者) */
    const ANONYMOUS = "Anonymous";
    /** 利用者(主にBtoCの顧客, BtoB提供先社員) */
    const USER = "User";
    /** 内部利用者(主にBtoCの社員, BtoB提供元社員) */
    const INTERNAL = "Internal";
    /** システム管理者(ITシステム担当社員またはシステム管理会社の社員) */
    const ADMINISTRATOR = "Administrator";
    /** システム(システム上の自動処理) */
    const SYSTEM = "System";

    public static function isAnonymous($type): bool
    {
        return $type === self::ANONYMOUS;
    }
    public static function isSystem($type): bool
    {
        return $type === self::SYSTEM;
    }
    public static function notSystem($type)
    {
        return !self::isSystem($type);
    }
}
