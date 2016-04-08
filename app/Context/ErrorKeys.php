<?php
namespace App\Context;

/** 審査例外で用いるメッセージキー定数 */
interface ErrorKeys
{
    /** サーバー側で問題が発生した可能性があります */
    const EXCEPTION = "error.Exception";
    /** 情報が見つかりませんでした */
    const ENTITY_NOT_FOUND = "error.EntityNotFoundException";
    /** ログイン状態が有効ではありません */
    const AUTHENTICATION = "error.Authentication";
    /** 対象機能の利用が認められていません */
    const ACCESS_DENIED = "error.AccessDeniedException";

    /** ログインに失敗しました */
    const LOGIN = "error.login";
    /** 既に登録されているIDです */
    const DUPLICATE_ID = "error.duplicateId";

    /** 既に処理済の情報です */
    const ACTION_UNPROCESSING = "error.ActionStatusType.unprocessing";
}
