<?php
namespace App\Models;

/**
 * 汎用ドメインで用いるメッセージキー定数。
 */
interface DomainErrorKeys
{
    /** マイナスを含めない数字を入力してください */
    const ABS_AMOUNT_ZERO = "error.domain.AbsAmount.zero";
}
