<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
     */

    'accepted' => '承認してください。',
    'active_url' => '有効なURLではありません。',
    'after' => ':date以降の日付を指定してください。',
    'alpha' => 'アルファベッドのみ使用できます。',
    'alpha_dash' => "英数字('A-Z','a-z','0-9')とハイフンと下線('-','_')が使用できます。",
    'alpha_num' => "英数字('A-Z','a-z','0-9')が使用できます。",
    'array' => '配列を指定してください。',
    'before' => ':date以前の日付を指定してください。',
    'between' => [
        'numeric' => ':minから、:maxまでの数字を指定してください。',
        'file' => ':min KBから:max KBまでのサイズのファイルを指定してください。',
        'string' => ':min文字から:max文字にしてください。',
        'array' => ':attributeの項目は、:min個から:max個にしてください。',
    ],
    'boolean' => "'true'か'false'を指定してください。",
    'confirmed' => ':attributeと:attribute確認が一致しません。',
    'date' => '正しい日付ではありません。',
    'date_format' => "形式は、':format'と合いません。",
    'different' => ':attributeと:otherには、異なるものを指定してください。',
    'digits' => ':digits桁にしてください。',
    'digits_between' => ':min桁から:max桁にしてください。',
    'distinct' => 'field has a duplicate value.',
    'email' => '有効なメールアドレス形式で指定してください。',
    'exists' => '選択されたものは有効ではありません。',
    'filled' => '必須です。',
    'image' => '画像を指定してください。',
    'in' => '選択されたものは有効ではありません。',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => '整数を指定してください。',
    'ip' => '有効なIPアドレスを指定してください。',
    'json' => '有効なJSON文字列を指定してください。',
    'max' => [
        'numeric' => ':max以下の数字を指定してください。',
        'file' => ':max KB以下のファイルを指定してください。',
        'string' => ':max文字以下にしてください。',
        'array' => ':max個以下にしてください。',
    ],
    'mimes' => ':valuesタイプのファイルを指定してください。',
    'min' => [
        'numeric' => ':min以上の数字を指定してください。',
        'file' => ':min KB以上のファイルを指定してください。',
        'string' => ':min文字以上にしてください。',
        'array' => ':max個以上にしてください。',
    ],
    'not_in' => '選択されたものは、有効ではありません。',
    'numeric' => '数字を指定してください。',
    'present' => 'The :attribute field must be present.',
    'regex' => '有効な正規表現を指定してください。',
    'required' => '必ず入力してください。',
    'required_if' => ':otherが:valueの場合、:attributeを指定してください。',
    'required_unless' => ':otherが:value以外の場合、:attributeを指定してください。',
    'required_with' => ':valuesが指定されている場合、:attributeも指定してください。',
    'required_with_all' => ':valuesが全て指定されている場合、:attributeも指定してください。',
    'required_without' => ':valuesが指定されていない場合、:attributeを指定してください。',
    'required_without_all' => ':valuesが全て指定されていない場合、:attributeを指定してください。',
    'same' => ':attributeと:otherが一致しません。',
    'size' => [
        'numeric' => ':sizeを指定してください。',
        'file' => ':size KBのファイルを指定してください。',
        'string' => ':size文字にしてください。',
        'array' => ':size個にしてください。',
    ],
    'string' => '文字を指定してください。',
    'timezone' => '有効なタイムゾーンを指定してください。',
    'unique' => '入力された値は既に使用されています。',
    'url' => '有効なURL形式で指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
     */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'Exception' => 'サーバー側で問題が発生した可能性があります',
    'EntityNotFoundException' => '情報が見つかりませんでした',
    'Authentication' => 'ログイン状態が有効ではありません',
    'AccessDeniedException' => '対象機能の利用が認められていません',
    'login' => 'ログインに失敗しました',
    'duplicateId' => '既に登録されているIDです',
    'ActionStatusType' => [
        'unprocessing' => '既に処理済の情報です',
    ],
    'domain' => [
        'AbsAmount' => ['zero' => 'マイナスを含めない数字を入力してください'],
    ],
    'Cashflow' => [
        'realizeDay' => '受渡日を迎えていないため実現できません',
        'beforeEqualsDay' => '既に受渡日を迎えています',
    ],
    'CashInOut' => [
        'afterEqualsDay' => '未到来の受渡日です',
        'beforeEqualsDay' => '既に発生日を迎えています',
        'withdrawAmount' => '出金可能額を超えています',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
     */

    'attributes' => [],

];
