<?php
namespace App\Context;

/**
 * 処理時の実行例外を表現します。
 * <p>復旧不可能なシステム例外をラップする目的で利用してください。
 */
class InvocationException extends \Exception
{
    public function __construct(string $message, \Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }

    public static function of(string $message): InvocationException
    {
        return new InvocationException($message);
    }
    public static function ofException(\Exception $e): InvocationException
    {
        return new InvocationException($e->getMessage(), $e);
    }
}
