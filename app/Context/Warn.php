<?php
namespace App\Context;

/**
 * 審査例外情報を表現します。
 */
class Warn
{
    /** 審査例外フィールドキー */
    public $field;
    /** 審査例外メッセージ */
    public $message;
    /** 審査例外メッセージ引数 */
    public $messageArgs;

    private function __construct(string $field, string $message, array $messageArgs)
    {
        $this->field = $field;
        $this->message = $message;
        $this->messageArgs = $messageArgs;
    }

    public static function of(string $message, $messageArgs = []): Warn
    {
        return new Warn("", $message, $messageArgs);
    }

    public static function ofField(string $field, string $message, $messageArgs = []): Warn
    {
        return new Warn($field, $message, $messageArgs);
    }
}
