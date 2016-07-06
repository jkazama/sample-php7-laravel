<?php
namespace App\Utils;

use Carbon\Carbon;

/**
 * フォーマットユーティリティ。
 */
class Formatter
{
    public static function dateStr($date)
    {
        if (empty($date)) return null;
        if ($date instanceof Carbon) {
            return $date->toIso8601String();
        } else if ($date instanceof \DateTime) {
            return $date->format(DATE_ISO8601);
        } else {
            return $date;
        }
    }
}