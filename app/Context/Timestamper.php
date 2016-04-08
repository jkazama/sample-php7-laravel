<?php
namespace App\Context;

/**
 * 日時ユーティリティコンポーネント。
 * low: AppSettingHandler の概念を追加
 */
class Timestamper
{
    private $day = null;

    /** 営業日を返します。 */
    public function day(): \DateTimeImmutable
    {
        if (isset($day)) {
            return $day;
        } else {
            $day = new \DateTimeImmutable();
            return $day->setTime(0, 0);
        }
    }

    /** 日時を返します。 */
    public function date(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    /** 営業日/日時を返します。 ( key: day, date ) */
    public function tp(): array
    {
        return ['day' => $this->day(), 'date' => $this->date()];
    }

    /** 営業日を指定日へ進めます。 */
    public function proceedDay(\DateTimeImmutable $day): Timestamper
    {
        $this->day = $day;
        return $this;
    }

}
