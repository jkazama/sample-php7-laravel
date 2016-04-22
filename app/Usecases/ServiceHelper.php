<?php
namespace App\Usecases;

use App\Context\DomainHelper;
use App\Models\BusinessDayHandler;

/**
 * アプリケーション処理で利用するコンポーネントへのアクセサを提供します。
 */
class ServiceHelper
{
    /** DomainHelper */
    public $dh;
    /** BusinessDayHandler */
    public $businessDay;

    public function __construct(DomainHelper $dh)
    {
        $this->dh = $dh;
        $this->businessDay = new BusinessDayHandler($dh->time);
    }
}
