<?php

namespace App\Context;

/**
 * 何らかの行為に関わる処理ステータス概念。
 */
class ActionStatusType
{
    /** 未処理 */
    const UNPROCESSED = "Unprocessed";
    /** 処理中 */
    const PROCESSING = "Processing";
    /** 処理済 */
    const PROCESSED = "Processed";
    /** 取消 */
    const CANCELLED = "Cancelled";
    /** エラー */
    const ERROR = "Error";

    /** 完了済みのステータス一覧 */
    public static function finishTypes(): array
    {
        return [self::PROCESSED, self::CANCELLED];
    }
    /** 未完了のステータス一覧(処理中は含めない) */
    public static function unprocessingTypes(): array
    {
        return [self::UNPROCESSED, self::ERROR];
    }
    /** 未完了のステータス一覧(処理中も含める) */
    public static function unprocessedTypes(): array
    {
        return [self::UNPROCESSED, self::PROCESSING, self::ERROR];
    }

    /** 完了済みのステータスの時はtrue */
    public static function isFinish(string $statusType): bool
    {
        return in_array($statusType, self::finishTypes());
    }

    /** 未完了のステータス(処理中は含めない)の時はtrue */
    public static function isUnprocessing(string $statusType): bool
    {
        return in_array($statusType, self::unprocessingTypes());
    }

    /** 未完了のステータス(処理中も含める)の時はtrue */
    public static function isUnprocessed(string $statusType): bool
    {
        return in_array($statusType, self::unprocessedTypes());
    }
}
