<?php
namespace App\Models;

use App\Context\Timestamper;

/**
 * ドメインに依存する営業日関連のユーティリティハンドラ。
 */
class BusinessDayHandler
{
    public $time;

    public function __construct(Timestamper $time)
    {
        $this->time = $time;
    }

    /** 営業日を返します。 */
    public function day($daysToAdd = 0): \DateTimeImmutable
    {
        $day = $this->time->day();
        return $daysToAdd !== 0 ? $day->modify('+' . $daysToAdd . ' day') : $day;
    }
}
