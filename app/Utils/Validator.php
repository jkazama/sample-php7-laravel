<?php

namespace App\Utils;

use App\Context\ValidationException;
use App\Context\Warns;

/**
 * 審査例外の構築概念を表現します。
 */
class Validator
{
    private $warns;

    public function __construct()
    {
        $this->warns = Warns::init();
    }

    /**
     * 審査処理をおこないます。
     * @param function $proc Validator インスタンスを引数に取る Callback 関数
     */
    public static function validate($proc)
    {
        $validator = new Validator();
        $proc($validator);
        $validator->verifyAll();
    }

    /** 審査をおこないます。validがfalseの時に例外を内部にスタックします。 */
    public function check(bool $valid, string $message): Validator
    {
        if (!$valid) {
            $this->warns->add($message);
        }
        return $this;
    }

    /** 個別属性の審査を行います。validがfalseの時に例外を内部にスタックします。 */
    public function checkField(bool $valid, string $field, string $message)
    {
        if (!$valid) {
            $this->warns->addField($field, $message);
        }
        return $this;
    }

    /** 審査を行います。失敗した時は即時に例外を発生させます。 */
    public function verify(bool $valid, string $message): Validator
    {
        return $this->check($valid, $message)->verifyAll();
    }

    /** 個別属性の審査を行います。失敗した時は即時に例外を発生させます。 */
    public function verifyField(bool $valid, string $field, string $message): Validator
    {
        return $this->checkField($valid, $field, $message)->verifyAll();
    }

    /** 検証します。事前に行ったcheckで例外が存在していた時は例外を発生させます。 */
    public function verifyAll(): Validator
    {
        if ($this->hasWarn()) {
            throw ValidationException::ofWarns($this->warns);
        }
        return $this->clear();
    }

    /** 審査例外を保有している時はtrueを返します。  */
    public function hasWarn(): bool
    {
        return $this->warns->nonEmpty();
    }

    /** 内部に保有する審査例外を初期化します。 */
    public function clear(): Validator
    {
        $this->warns = Warns::init();
        return $this;
    }
}
