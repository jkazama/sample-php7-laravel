<?php
namespace App\Context\Actor;

/**
 * 利用者セッション。
 * low: とりあえずベタに $_SESSION に依存させるアプローチで対応
 */
class ActorSession
{
    const ID_ACTOR_SESSION = "ActorSession";

    /** 利用者セッションへ利用者を紐付けます。 */
    public function bind(Actor $actor): ActorSession
    {
        $this->inSession(function () use ($actor) {
            $_SESSION[self::ID_ACTOR_SESSION] = $actor;
        });
        return $this;
    }

    /** 利用者セッションを破棄します。 */
    public function unbind(): ActorSession
    {
        $this->inSession(function () {
            unset($_SESSION[self::ID_ACTOR_SESSION]);
        });
        return $this;
    }

    /** 有効な利用者を返します。紐付けされていない時は匿名者が返されます。 */
    public function actor(): Actor
    {
        return $this->inSession(function () {
            return $_SESSION[self::ID_ACTOR_SESSION] ?? Actor::anonymous();
        });
    }

    private function inSession($proc)
    {
        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            return $proc();
        } finally {
            session_write_close();
        }
    }

}
