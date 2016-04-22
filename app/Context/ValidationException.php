<?php
namespace App\Context;

/**
 * 審査例外を表現します。
 * <p>ValidationExceptionは入力例外や状態遷移例外等の復旧可能な審査例外です。
 * その性質上ログ等での出力はWARNレベル(ERRORでなく)で行われます。
 * <p>審査例外はグローバル/フィールドスコープで複数保有する事が可能です。複数件の例外を取り扱う際は
 * Warnsを利用して初期化してください。
 */
class ValidationException extends \Exception
{
    public $warns;

    /**
     * @param string $message
     * @param Exception|null $previous
     */
    public function __construct(Warns $warns, \Exception $previous = null)
    {
        parent::__construct($warns->head() ?? ErrorKeys::Exception, 400, $previous);
        $this->warns = $warns;
    }

    /** 発生した審査例外 ( Warn ) 一覧を返します。*/
    function list(): array
    {
        return $this->warns->values();
    }

    public static function of(string $message)
    {
        return self::ofWarns(Warns::init($message));
    }
    public static function ofField(string $field, string $message, array $messageArgs = [])
    {
        return self::ofWarns(Warns::init($message, $messageArgs));
    }
    public static function ofException(\Exception $e): ValidationException
    {
        return new ValidationException(Warns::init($e->getMessage()), $e);
    }
    public static function ofWarns(Warns $warns): ValidationException
    {
        return new ValidationException($warns);
    }

}
