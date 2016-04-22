<?php
namespace App\Usecases;

use DB;

trait ServiceSupport
{
    /**
     * トランザクション処理をおこないます。
     * low: 本処理のように何もしないなら DB クラスを直で実行してしまう方が良い。
     * ロックやクエリトレース等の差込をおこないたいなら、本処理に絡めて行く。
     */
    protected function tx(\Closure $proc)
    {
        return DB::transaction($proc);
    }
}
