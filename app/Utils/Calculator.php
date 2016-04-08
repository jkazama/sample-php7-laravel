<?php
namespace App\Utils;

/**
 * 計算ユーティリティ。
 * <p>単純計算の簡易化を目的とした割り切った実装なのでスレッドセーフではありません。
 */
class Calculator
{
    // 切り上げ
    const ROUNDING_UP = 1;
    // 切り捨て
    const ROUNDING_DOWN = 2;
    // 四捨五入
    const ROUNDING_HALF_UP = 3;

    private $value = 0;
    /** 小数点以下桁数 */
    private $scale = 0;
    /** 端数定義。標準では切捨て */
    private $mode = self::ROUNDING_DOWN;
    /** 計算の都度端数処理をする時はtrue */
    private $roundingAlways = false;

    private function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * 計算前処理定義。
     * @param int $scale 小数点以下桁数
     * @param int $mode 端数定数
     * @return Calculator
     */
    public function scale(int $scale, $mode = self::ROUNDING_DOWN): Calculator
    {
        $this->scale = $scale;
        $this->mode = $mode;
        return $this;
    }

    /**
     * 計算前の端数処理定義をします。
     * @param bool $roundingAlways 計算の都度端数処理をする時はtrue
     * @return Calculator
     */
    public function roundingAlways(bool $roundingAlways): Calculator
    {
        $this->roundingAlways = $roundingAlways;
        return $this;
    }

    /** 与えた計算値を自身が保持する値に加えます。 */
    public function add(float $v): Calculator
    {
        return $this->roundingValueSet($this->value + $v);
    }

    private function roundingValueSet(float $v): Calculator
    {
        $this->value = $this->roundingAlways ? $this->rounding($v) : $v;
        return $this;
    }

    private function rounding(float $v): float
    {
        switch ($this->mode) {
            case self::ROUNDING_DOWN:
                $fig = pow(10, $this->scale);
                return floor($v * $fig) / $fig;
            case self::ROUNDING_UP:
                $fig = pow(10, $this->scale);
                return ceil($v * $fig) / $fig;
            default:
                return round($v, $this->scale);
        }
    }

    /** 自身が保持する値へ与えた計算値を引きます。 */
    public function subtract(float $v): Calculator
    {
        return $this->roundingValueSet($this->value - $v);
    }

    /** 自身が保持する値へ与えた計算値を掛けます。 */
    public function multiply(float $v): Calculator
    {
        return $this->roundingValueSet($this->value * $v);
    }

    /** 自身が保持する値へ与えた計算値を掛けます。 */
    public function divideBy(float $v): Calculator
    {
        return $this->roundingValueSet($this->value / $v);
    }

    /** 計算結果を int 型で返します。*/
    public function integer(): int
    {
        return (int) $this->float();
    }

    /** 計算結果を float 型で返します。*/
    public function float(): float
    {
        return $this->rounding($this->value);
    }

    /**
     * @return Calculator
     */
    public static function init(): Calculator
    {
        return static::of(0);
    }

    /**
     * @param number $value 初期値
     * @return Calculator
     */
    public static function of(float $value): Calculator
    {
        return new Calculator($value);
    }
}
