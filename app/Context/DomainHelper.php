<?php
namespace App\Context;

use App\Context\Actor\Actor;
use App\Context\Actor\ActorSession;

/**
 * ドメイン処理を行う上で必要となるインフラ層コンポーネントへのアクセサを提供します。
 */
class DomainHelper
{
    /** ActorSession */
    public $actorSession;
    /** Timestamper */
    public $time;

    public function __construct()
    {
        $this->actorSession = new ActorSession();
        $this->time = new Timestamper();
    }

    /** ログイン中のユースケース利用者を取得します。 */
    public function actor(): Actor
    {
        return $this->actorSession->actor();
    }
}
