<?php
namespace App\Context\Actor;

/**
 * ユースケースにおける利用者を表現します。
 */
class Actor
{
    /** 利用者ID */
    public $id;
    /** 利用者名称 */
    public $name;
    /** 利用者が持つ{@link ActorRoleType} */
    public $roleType;
    /** 利用者が使用する{@link Locale} */
    public $locale = "";
    /** 利用者の接続チャネル名称 */
    public $channel = "";
    /** 利用者を特定する外部情報。(IPなど) */
    public $source = "";
    /** 権限一覧 */
    public $authorities = [];

    /** 匿名利用者を返します。 */
    public static function anonymous(): Actor
    {
        return self::of("unknown", ActorRoleType::ANONYMOUS);
    }

    /** システム利用者を返します。 */
    public static function system(): Actor
    {
        return self::of("system", ActorRoleType::SYSTEM);
    }

    public static function of(string $id, string $roleType)
    {
        return self::ofName($id, $id, $roleType);
    }
    public static function ofName(string $id, string $name, string $roleType)
    {
        $m = new Actor();
        $m->id = $id;
        $m->name = $name;
        $m->roleType = $roleType;
        return $m;
    }

}
